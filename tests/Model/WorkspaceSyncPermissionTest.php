<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Tests\Model;

use Pimcore;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Configuration\Dao;
use Pimcore\Bundle\DataHubBundle\EventListener\DataChangeListener;
use Pimcore\Db;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\User;
use Pimcore\Security\User\TokenStorageUserResolver;
use Pimcore\Security\User\User as SecurityUser;
use Pimcore\Tests\Support\Test\ModelTestCase;
use Pimcore\Tests\Support\Util\TestHelper;
use ReflectionProperty;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Moving, renaming or deleting an element keeps the workspaces of all configurations in sync. That
 * is a system side effect and must not depend on the data hub rights of the user who triggered it.
 *
 * @group model.workspace.sync
 */
class WorkspaceSyncPermissionTest extends ModelTestCase
{
    private const CONFIG_NAME = 'workspace_sync_permission';

    private ?TokenInterface $originalToken = null;

    private ?User $restrictedUser = null;

    public function setUp(): void
    {
        parent::setUp();
        TestHelper::cleanUp();

        $this->originalToken = $this->tokenStorage()->getToken();

        $user = new User();
        $user->setParentId(0);
        $user->setName('workspace_sync_user_' . uniqid());
        $user->setActive(true);
        $user->setAdmin(false);
        $user->setPermissions(['objects']);
        $user->save();
        $this->restrictedUser = $user;

        $this->resetConfigurationCatalog();
    }

    public function tearDown(): void
    {
        $this->tokenStorage()->setToken($this->originalToken);

        Configuration::getByName(self::CONFIG_NAME)?->delete();
        $this->resetConfigurationCatalog();
        $this->restrictedUser?->delete();
        $this->restrictedUser = null;
        TestHelper::cleanUp();

        parent::tearDown();
    }

    public function testRenameByUserWithoutDataHubPermissionUpdatesWorkspacePath(): void
    {
        $folder = $this->createFolderWithWorkspace('sync-source');
        $oldPath = $folder->getRealFullPath();

        // The element is renamed in the database and the event is dispatched the way Pimcore does after a
        // path change, which keeps the search index subscribers of a real save out of this test.
        $db = Db::get();
        $db->update('objects', [$db->quoteIdentifier('key') => 'sync-renamed'], ['id' => $folder->getId()]);
        $folder = Folder::getById($folder->getId(), ['force' => true]);
        $event = new DataObjectEvent($folder, ['oldPath' => $oldPath]);

        $this->loginAs($this->restrictedUser);
        (new DataChangeListener())->onObjectUpdate($event);

        $this->assertSame(
            ['/sync-renamed'],
            $this->getWorkspacePaths(),
            'The workspace must follow the renamed element'
        );
    }

    public function testDeleteByUserWithoutDataHubPermissionRemovesWorkspace(): void
    {
        $folder = $this->createFolderWithWorkspace('sync-deleted');

        $this->loginAs($this->restrictedUser);
        $folder->delete();

        $this->assertSame([], $this->getWorkspacePaths(), 'The workspace of a deleted element must be removed');
    }

    public function testSaveStillRequiresPermissionOfCurrentUser(): void
    {
        $this->createFolderWithWorkspace('sync-guarded');
        $configuration = Configuration::getByName(self::CONFIG_NAME);

        $this->loginAs($this->restrictedUser);

        $this->expectExceptionMessage('Permissions missing to save the configuration');
        $configuration->save();
    }

    private function createFolderWithWorkspace(string $key): Folder
    {
        $folder = Folder::create([
            'parentId' => 1,
            'key' => $key,
            'userOwner' => 1,
            'userModification' => 1,
        ]);

        $configuration = new Configuration('graphql', null, self::CONFIG_NAME, [
            'general' => ['active' => false],
            'workspaces' => [
                'object' => [
                    [
                        'cpath' => $folder->getRealFullPath(),
                        'read' => true,
                        'create' => false,
                        'update' => false,
                        'delete' => false,
                        'publish' => false,
                    ],
                ],
            ],
        ]);
        // no user is logged in on the command line, so saving the fixture is allowed
        $configuration->save();
        $this->resetConfigurationCatalog();

        return $folder;
    }

    /**
     * @return string[]
     */
    private function getWorkspacePaths(): array
    {
        $configuration = Configuration::getByName(self::CONFIG_NAME);

        return array_column($configuration->getConfiguration()['workspaces']['object'] ?? [], 'cpath');
    }

    private function resetConfigurationCatalog(): void
    {
        // The list of configurations is cached statically and neither save() nor delete() invalidate it, so
        // a catalog built before or after this fixture existed would hide it or still name it later on.
        (new ReflectionProperty(Dao::class, '_config'))->setValue(null, null);
    }

    private function loginAs(User $user): void
    {
        $this->tokenStorage()->setToken(new UsernamePasswordToken(new SecurityUser($user), 'pimcore_admin'));
    }

    private function tokenStorage(): TokenStorageInterface
    {
        // security.token_storage is inlined out of the compiled container and cannot be fetched by id.
        // Admin::getCurrentUser() reads the token storage held by the public TokenStorageUserResolver
        // service, so the same shared instance is reached through that resolver.
        $resolver = Pimcore::getContainer()->get(TokenStorageUserResolver::class);

        return (new ReflectionProperty(TokenStorageUserResolver::class, 'tokenStorage'))->getValue($resolver);
    }
}
