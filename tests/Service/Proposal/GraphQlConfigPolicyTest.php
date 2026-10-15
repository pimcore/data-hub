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
use LogicException;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigDocument;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\GraphQlConfigPolicy;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;

class GraphQlConfigPolicyTest extends Unit
{
    private const string STORED = 'policy-test-endpoint';

    private const string PATH = '/Product Data';

    protected function tearDown(): void
    {
        Configuration::getByName(self::STORED)?->getDao()->delete();
    }

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
            'workspaces' => ['object' => [['cpath' => self::PATH, 'read' => true, 'update' => true]]],
            'schema' => ['specialEntities' => ['asset' => ['read' => true, 'delete' => true]]],
        ], false);

        $this->assertCount(1, $problems);
        $this->assertStringContainsString('workspaces.object /Product Data update', $problems[0]);
        $this->assertStringContainsString('schema.specialEntities.asset delete', $problems[0]);
    }

    public function testAWriteFlagCountsUnderEitherNameAndAsAnyTruthyValue(): void
    {
        $problems = $this->policy()->problems([
            'general' => ['name' => 'no-such-endpoint'],
            'workspaces' => ['document' => [['cpath' => '/shop', 'read' => true, 'delete' => 'true']]],
            'schema' => [
                'specialEntities' => ['asset' => ['read' => true, 'createAllowed' => true]],
                'mutationEntities' => ['Car' => ['id' => 'Car', 'create' => 1]],
            ],
        ], false);

        $refusal = $this->problemContaining('Write access cannot be proposed', $problems);
        $this->assertStringContainsString('workspaces.document /shop delete', $refusal);
        $this->assertStringContainsString('schema.specialEntities.asset create', $refusal);
        $this->assertStringContainsString('schema.mutationEntities.Car create', $refusal);
    }

    public function testAWriteGrantTheEndpointAlreadyHasIsKept(): void
    {
        $document = [
            'general' => ['name' => self::STORED],
            'workspaces' => ['object' => [['cpath' => self::PATH, 'read' => true, 'update' => true]]],
        ];
        $this->store('graphql', $document);

        $this->assertSame([], $this->policy()->problems($document, false));

        $document['workspaces']['object'][0]['delete'] = true;
        $problems = $this->policy()->problems($document, false);
        $this->assertCount(1, $problems);
        $this->assertStringContainsString('workspaces.object /Product Data delete', $problems[0]);
        $this->assertStringNotContainsString('update', $problems[0]);
    }

    public function testANameAnotherAdapterHoldsIsRefused(): void
    {
        $this->store('webhook', ['general' => ['name' => self::STORED]]);

        $problems = $this->policy()->problems(['general' => ['name' => self::STORED]], true);

        $this->assertCount(1, $problems);
        $this->assertStringContainsString('A webhook configuration is already stored under this name', $problems[0]);
    }

    public function testAnEntityKeyedByAnythingButItsIdIsRefused(): void
    {
        $problems = $this->policy()->problems([
            'general' => ['name' => 'no-such-endpoint'],
            'schema' => ['queryEntities' => [
                'Car' => ['id' => 'Product', 'columnConfig' => []],
                'Bike' => ['columnConfig' => []],
            ]],
        ], true);

        $this->problemContaining('schema.queryEntities.Car needs "id": "Car"', $problems);
        $this->problemContaining('schema.queryEntities.Bike needs "id": "Bike"', $problems);
    }

    public function testAnUnknownClassIsRefused(): void
    {
        $problems = $this->policy()->problems([
            'general' => ['name' => 'no-such-endpoint'],
            'schema' => ['queryEntities' => ['NoSuchClass' => ['id' => 'NoSuchClass']]],
        ], true);

        $this->assertSame(
            ['"NoSuchClass" is not the name of a data object class this installation has (schema.queryEntities).'],
            $problems,
        );
    }

    public function testANewConfigurationNeedsAQueryEntity(): void
    {
        $problems = $this->policy()->problems(['general' => ['name' => 'no-such-endpoint']], true);

        $this->assertCount(1, $problems);
        $this->assertStringContainsString('at least one query entity', $problems[0]);
        $this->assertSame([], $this->policy()->problems(['general' => ['name' => 'no-such-endpoint']], false));
    }

    public function testSavingOverAnotherAdaptersConfigurationIsRefused(): void
    {
        $configurations = $this->createMock(ConfigurationServiceInterface::class);
        $configurations->expects($this->never())->method('updateConfiguration');

        $this->expectException(LogicException::class);

        (new GraphQlConfigPolicy($configurations))->save('orders', ['general' => ['type' => 'webhook']]);
    }

    public function testReadAccessIsProposable(): void
    {
        $problems = $this->policy()->problems([
            'general' => ['name' => 'no-such-endpoint'],
            'workspaces' => ['object' => [['cpath' => self::PATH, 'read' => true, 'update' => false]]],
            'schema' => ['specialEntities' => ['asset' => ['read' => true]]],
        ], false);

        $this->assertSame([], $problems);
    }

    public function testSavingListsTheGenericTypesTheWayTheEditorSendsThem(): void
    {
        $saved = null;
        $capture = static function (array $configuration) use (&$saved): bool {
            $saved = $configuration;

            return true;
        };
        $configurations = $this->createMock(ConfigurationServiceInterface::class);
        $configurations->expects($this->once())
            ->method('updateConfiguration')
            ->with($this->anything(), $this->callback($capture))
            ->willReturn(1);

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

    /**
     * @param list<string> $problems
     */
    private function problemContaining(string $needle, array $problems): string
    {
        foreach ($problems as $problem) {
            if (str_contains($problem, $needle)) {
                return $problem;
            }
        }

        $this->fail(sprintf('No problem contains "%s": %s', $needle, implode(' | ', $problems)));
    }

    /**
     * Stored through the DAO alone: the model's save drops workspaces on paths the test
     * installation lacks and writes the workspace tables, neither of which the policy reads.
     *
     * @param array<string, mixed> $document
     */
    private function store(string $type, array $document): void
    {
        $document['general'] += ['name' => self::STORED, 'type' => $type, 'path' => ''];
        $configuration = new Configuration($type, '', self::STORED, $document);
        $configuration->getDao()->save();
    }

    private function policy(): GraphQlConfigPolicy
    {
        return new GraphQlConfigPolicy($this->createStub(ConfigurationServiceInterface::class));
    }
}
