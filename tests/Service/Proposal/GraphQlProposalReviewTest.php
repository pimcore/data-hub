<?php

declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Tests\Service\Proposal;

use Codeception\Test\Unit;
use Pimcore\Bundle\ChangeControlBundle\Contract\ChangeControlAuthoringInterface;
use Pimcore\Bundle\ChangeControlBundle\Installer\Installer as ChangeControlInstaller;
use Pimcore\Bundle\ChangeControlBundle\Storage\SubjectIdentityRepository;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectRef;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Configuration\Workspace\Dao as WorkspaceDao;
use Pimcore\Bundle\DataHubBundle\Installer;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Db;
use Pimcore\Model\User;
use Pimcore\Security\User\User as SecurityUser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Uid\Uuid;

/**
 * A GraphQL proposal through Change Control as the review lane runs it: recorded on a draft,
 * then approved or rejected. Change Control is an optional peer, so this skips where it is not
 * installed.
 */
class GraphQlProposalReviewTest extends Unit
{
    private const string SUBJECT_TYPE = 'data-hub-graphql-config';

    private const string APPROVED = 'proposal-review-approved';

    private const string REJECTED = 'proposal-review-rejected';

    private ?User $reviewer = null;

    /** the chat session an agent's draft is opened in; only that context sees it */
    private string $session = '';

    protected function setUp(): void
    {
        if (!interface_exists(ChangeControlAuthoringInterface::class)
            || !$this->services()->has(ChangeControlAuthoringInterface::class)) {
            $this->markTestSkipped('Change Control is not installed.');
        }

        $changeControl = $this->services()->get(ChangeControlInstaller::class);
        $this->assertInstanceOf(ChangeControlInstaller::class, $changeControl);
        $changeControl->createMissingTables();

        // this suite does not run the bundle's installer, which creates the workspace tables
        if (!Db::get()->createSchemaManager()->tablesExist([WorkspaceDao::TABLE_NAME_DATAOBJECT])) {
            $installer = $this->services()->get(Installer::class);
            $this->assertInstanceOf(Installer::class, $installer);
            $installer->install();
        }

        $this->session = 'chat-' . bin2hex(random_bytes(8));
        $this->reviewer = User::create([
            'parentId' => 0,
            'name' => 'proposal-review-' . bin2hex(random_bytes(4)),
            'admin' => true,
            'active' => true,
        ]);

        // approving is a reviewer's request: the Studio save reads them as the current user
        $this->tokens()->setToken(new UsernamePasswordToken(new SecurityUser($this->reviewer), 'pimcore_studio'));
    }

    protected function tearDown(): void
    {
        foreach ([self::APPROVED, self::REJECTED] as $name) {
            $configuration = Configuration::getByName($name);
            if ($configuration !== null) {
                WorkspaceHelper::deleteConfiguration($configuration);
                $configuration->getDao()->delete();
            }
        }

        if ($this->reviewer !== null) {
            $this->tokens()->setToken(null);
            $this->reviewer->delete();
        }
    }

    public function testAnApprovedProposalIsStoredOnlyOnApproval(): void
    {
        $authoring = $this->authoring();
        $branch = $this->open(self::APPROVED);
        $authoring->record($branch, $this->document(self::APPROVED), $this->reviewerId(), $this->session);

        $this->assertNull(Configuration::getByName(self::APPROVED), 'recording a proposal writes nothing');
        $this->assertSame([], $this->workspaceRows(self::APPROVED));

        $authoring->merge($branch, $this->reviewer(), contextRef: $this->session);

        $stored = Configuration::getByName(self::APPROVED)?->getConfiguration();
        $this->assertIsArray($stored);
        $this->assertSame('graphql', $stored['general']['type']);
        $this->assertSame(self::APPROVED, $stored['general']['name']);
        $this->assertSame('Cars', $stored['general']['description']);
        $this->assertFalse($stored['general']['active']);
        $this->assertSame(['Car'], array_keys($stored['schema']['queryEntities']));
        $this->assertSame('Car', $stored['schema']['queryEntities']['Car']['id']);

        $rows = $this->workspaceRows(self::APPROVED);
        $this->assertCount(1, $rows);
        $this->assertSame('/', $rows[0]['cpath']);
        $this->assertSame(1, (int) $rows[0]['read']);
        $this->assertSame(0, (int) $rows[0]['create']);
        $this->assertSame(0, (int) $rows[0]['update']);
        $this->assertSame(0, (int) $rows[0]['delete']);
    }

    public function testARejectedProposalLeavesNothingBehind(): void
    {
        $authoring = $this->authoring();
        $branch = $this->open(self::REJECTED);
        $authoring->record($branch, $this->document(self::REJECTED), $this->reviewerId(), $this->session);

        $this->assertTrue($authoring->discard($branch, $this->reviewerId(), $this->session));

        $this->assertNull(Configuration::getByName(self::REJECTED));
        $this->assertSame([], $this->workspaceRows(self::REJECTED));
    }

    /**
     * What propose_graphql_config records for a new name: the document, inactive, under the
     * subject's own identity.
     *
     * @return array<string, mixed>
     */
    private function document(string $name): array
    {
        return [
            'general' => [
                'name' => $name,
                'type' => 'graphql',
                'active' => false,
                'path' => '',
                'description' => 'Cars',
            ],
            'schema' => [
                'queryEntities' => ['Car' => ['id' => 'Car', 'name' => 'Car', 'columnConfig' => []]],
            ],
            'workspaces' => [
                'object' => [['cpath' => '/', 'read' => true, 'create' => false, 'update' => false, 'delete' => false]],
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function workspaceRows(string $name): array
    {
        return Db::get()->fetchAllAssociative(
            'SELECT * FROM ' . WorkspaceDao::TABLE_NAME_DATAOBJECT . ' WHERE configuration = ?',
            [$name],
        );
    }

    private function open(string $name): Uuid
    {
        $identities = $this->services()->get(SubjectIdentityRepository::class);
        $this->assertInstanceOf(SubjectIdentityRepository::class, $identities);
        $subject = new SubjectRef(self::SUBJECT_TYPE, $identities->resolve(self::SUBJECT_TYPE, $name), $name);

        return $this->authoring()->open(
            $subject,
            'A new endpoint',
            'agent',
            $this->reviewerId(),
            $this->session,
        )->uuid;
    }

    private function authoring(): ChangeControlAuthoringInterface
    {
        $authoring = $this->services()->get(ChangeControlAuthoringInterface::class);
        $this->assertInstanceOf(ChangeControlAuthoringInterface::class, $authoring);

        return $authoring;
    }

    private function reviewer(): User
    {
        $this->assertInstanceOf(User::class, $this->reviewer);

        return $this->reviewer;
    }

    private function reviewerId(): int
    {
        return (int) $this->reviewer()->getId();
    }

    private function tokens(): TokenStorageInterface
    {
        $tokens = $this->services()->get('security.token_storage');
        $this->assertInstanceOf(TokenStorageInterface::class, $tokens);

        return $tokens;
    }

    private function services(): ContainerInterface
    {
        $container = \Pimcore::getContainer();
        $this->assertInstanceOf(ContainerInterface::class, $container);
        $services = $container->has('test.service_container') ? $container->get('test.service_container') : $container;
        $this->assertInstanceOf(ContainerInterface::class, $services);

        return $services;
    }
}
