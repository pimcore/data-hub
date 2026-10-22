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
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Configuration\Workspace\Dao as WorkspaceDao;
use Pimcore\Bundle\DataHubBundle\Installer;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\GraphQlConfigPolicy;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Db;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * An approved proposal written through the real Studio save: dehydrator, model save and
 * workspace tables, as the editor writes it.
 */
class GraphQlConfigSaveTest extends Unit
{
    private const string NAME = 'policy-save-endpoint';

    private const array SECURITY = ['method' => 'datahub_apikey', 'apikey' => ['kept-as-stored-0123456789']];

    protected function setUp(): void
    {
        // this suite does not run the bundle's installer, which creates the workspace tables
        if (!Db::get()->createSchemaManager()->tablesExist([WorkspaceDao::TABLE_NAME_DATAOBJECT])) {
            $installer = \Pimcore::getContainer()->get(Installer::class);
            $this->assertInstanceOf(Installer::class, $installer);
            $installer->install();
        }

        $configuration = new Configuration('graphql', '', self::NAME, [
            'general' => ['name' => self::NAME, 'type' => 'graphql', 'path' => '', 'active' => false],
            'security' => self::SECURITY,
        ]);
        $configuration->getDao()->save();
    }

    protected function tearDown(): void
    {
        $configuration = Configuration::getByName(self::NAME);
        if ($configuration !== null) {
            WorkspaceHelper::deleteConfiguration($configuration);
            $configuration->getDao()->delete();
        }
    }

    public function testAnApprovedDocumentIsStoredAsProposed(): void
    {
        $this->policy()->save(self::NAME, [
            'general' => ['name' => self::NAME, 'type' => 'graphql', 'active' => false, 'description' => 'Cars'],
            'security' => self::SECURITY,
            'schema' => [
                'queryEntities' => ['Car' => ['id' => 'Car', 'name' => 'Car', 'columnConfig' => []]],
                'specialEntities' => [
                    'asset' => ['read' => true, 'create' => false, 'update' => false, 'delete' => false],
                ],
            ],
            'workspaces' => [
                'object' => [['cpath' => '/', 'read' => true, 'create' => false, 'update' => false, 'delete' => false]],
            ],
        ]);

        $stored = Configuration::getByName(self::NAME)?->getConfiguration();
        $this->assertIsArray($stored);
        $this->assertSame('Cars', $stored['general']['description']);
        $this->assertFalse($stored['general']['active']);
        $this->assertSame(self::SECURITY, $stored['security']);
        $this->assertSame(['Car'], array_keys($stored['schema']['queryEntities']));
        $this->assertSame('Car', $stored['schema']['queryEntities']['Car']['id']);
        $this->assertEquals(
            ['asset' => ['read' => true, 'create' => false, 'update' => false, 'delete' => false]],
            $stored['schema']['specialEntities'],
        );

        $rows = Db::get()->fetchAllAssociative(
            'SELECT * FROM ' . WorkspaceDao::TABLE_NAME_DATAOBJECT . ' WHERE configuration = ?',
            [self::NAME],
        );
        $this->assertCount(1, $rows);
        $this->assertSame('/', $rows[0]['cpath']);
        $this->assertSame(1, (int) $rows[0]['read']);
        $this->assertSame(0, (int) $rows[0]['create']);
        $this->assertSame(0, (int) $rows[0]['update']);
        $this->assertSame(0, (int) $rows[0]['delete']);
    }

    public function testGenericTypesSentAsAListAreStoredUnderTheirNames(): void
    {
        $this->policy()->save(self::NAME, [
            'general' => ['name' => self::NAME, 'type' => 'graphql', 'active' => false],
            'security' => self::SECURITY,
            'schema' => ['specialEntities' => [
                ['name' => 'document', 'readAllowed' => true],
                ['name' => 'asset', 'readAllowed' => true],
            ]],
        ]);

        $stored = Configuration::getByName(self::NAME)?->getConfiguration();
        $this->assertIsArray($stored);
        $this->assertEquals(
            [
                'document' => ['read' => true, 'create' => false, 'update' => false, 'delete' => false],
                'asset' => ['read' => true, 'create' => false, 'update' => false, 'delete' => false],
            ],
            $stored['schema']['specialEntities'],
        );
    }

    private function policy(): GraphQlConfigPolicy
    {
        $container = \Pimcore::getContainer();
        $services = $container->has('test.service_container') ? $container->get('test.service_container') : $container;
        $configurations = $services->get(ConfigurationServiceInterface::class);
        $this->assertInstanceOf(ConfigurationServiceInterface::class, $configurations);

        $events = $services->get('event_dispatcher');
        $this->assertInstanceOf(EventDispatcherInterface::class, $events);

        return new GraphQlConfigPolicy($configurations, $events);
    }
}
