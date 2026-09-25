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
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigDocument;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\GraphQlConfigPolicy;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;

class GraphQlConfigPolicyTest extends Unit
{
    public function testSecurityAndPermissionsAreRefusedAndWithheld(): void
    {
        $document = [
            'general' => ['name' => 'shop'],
            'security' => ['method' => 'datahub_apikey', 'apikey' => ['secret-secret-secret']],
            'workspaces' => ['object' => []],
            'permissions' => ['user' => []],
        ];

        $this->assertSame(['security', 'permissions'], ConfigDocument::refusedPaths($document, $this->policy()));
        $this->assertArrayNotHasKey('security', ConfigDocument::readable($document, $this->policy()));
    }

    public function testWriteAccessTheEndpointDoesNotHaveIsRefused(): void
    {
        $problems = $this->policy()->problems([
            'general' => ['name' => 'no-such-endpoint'],
            'workspaces' => ['object' => [['cpath' => '/Product Data', 'read' => true, 'update' => true]]],
            'schema' => ['specialEntities' => ['asset' => ['read' => true, 'delete' => true]]],
        ], false);

        $this->assertCount(1, $problems);
        $this->assertStringContainsString('workspaces.object /Product Data update', $problems[0]);
        $this->assertStringContainsString('schema.specialEntities.asset delete', $problems[0]);
    }

    public function testReadAccessIsProposable(): void
    {
        $problems = $this->policy()->problems([
            'general' => ['name' => 'no-such-endpoint'],
            'workspaces' => ['object' => [['cpath' => '/Product Data', 'read' => true, 'update' => false]]],
            'schema' => ['specialEntities' => ['asset' => ['read' => true]]],
        ], false);

        $this->assertSame([], $problems);
    }

    public function testSavingListsTheGenericTypesTheWayTheEditorSendsThem(): void
    {
        $saved = null;
        $configurations = $this->createMock(ConfigurationServiceInterface::class);
        $configurations->expects($this->once())
            ->method('updateConfiguration')
            ->willReturnCallback(static function (string $name, array $configuration) use (&$saved): int {
                $saved = $configuration;

                return 1;
            });

        (new GraphQlConfigPolicy($configurations))->save('shop', [
            'schema' => ['specialEntities' => ['asset' => ['read' => true, 'create' => false]]],
        ]);

        $this->assertSame(
            [[
                'name' => 'asset',
                'readAllowed' => true,
                'createAllowed' => false,
                'updateAllowed' => false,
                'deleteAllowed' => false,
            ]],
            $saved['schema']['specialEntities'],
        );
    }

    private function policy(): GraphQlConfigPolicy
    {
        return new GraphQlConfigPolicy($this->createStub(ConfigurationServiceInterface::class));
    }
}
