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
use Pimcore\Bundle\DataHubBundle\ConfigEvents;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Event\Config\SpecialEntitiesEvent;
use Pimcore\Bundle\DataHubBundle\Model\SpecialEntitySetting;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigDocument;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\GraphQlConfigPolicy;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class GraphQlConfigPolicyTest extends Unit
{
    private const string STORED = 'policy-test-endpoint';

    // the root folder: every installation has it, and a workspace on a missing path is refused
    private const string PATH = '/';

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
        $this->assertStringContainsString('workspaces.object / update', $problems[0]);
        $this->assertStringContainsString('schema.specialEntities.asset delete', $problems[0]);
    }

    public function testAWriteFlagCountsUnderEitherNameAndAsAnyTruthyValue(): void
    {
        $problems = $this->policy()->problems([
            'general' => ['name' => 'no-such-endpoint'],
            'workspaces' => ['document' => [['cpath' => '/', 'read' => true, 'delete' => 'true']]],
            'schema' => [
                'specialEntities' => ['asset' => ['read' => true, 'createAllowed' => true]],
                'mutationEntities' => ['Car' => ['id' => 'Car', 'create' => 1]],
            ],
        ], false);

        $refusal = $this->problemContaining('Write access cannot be proposed', $problems);
        $this->assertStringContainsString('workspaces.document / delete', $refusal);
        $this->assertStringContainsString('schema.specialEntities.asset create', $refusal);
        $this->assertStringContainsString('schema.mutationEntities.Car create', $refusal);
    }

    public function testAWriteFlagCountsUnderAnyCase(): void
    {
        $problems = $this->policy()->problems([
            'general' => ['name' => 'no-such-endpoint'],
            'workspaces' => [
                'object' => [['cpath' => '/', 'read' => true, 'CREATE' => true]],
                'asset' => [['cpath' => '/', 'read' => true, 'update' => false, 'Update' => 1]],
            ],
            'schema' => ['specialEntities' => ['asset' => ['read' => true, 'DeleteAllowed' => true]]],
        ], false);

        $refusal = $this->problemContaining('Write access cannot be proposed', $problems);
        $this->assertStringContainsString('workspaces.object / create', $refusal);
        $this->assertStringContainsString('workspaces.asset / update', $refusal);
        $this->assertStringContainsString('schema.specialEntities.asset delete', $refusal);
    }

    public function testSummaryNamesTheQueriesAndCountsTheMutations(): void
    {
        $this->assertSame(
            ['queryEntities' => ['Car', 'Product'], 'mutationEntityCount' => 1],
            $this->policy()->summarize(['schema' => [
                'queryEntities' => ['Car' => ['id' => 'Car'], 'Product' => ['id' => 'Product']],
                'mutationEntities' => ['Car' => ['id' => 'Car']],
            ]]),
        );
        $this->assertSame(
            ['queryEntities' => [], 'mutationEntityCount' => 0],
            $this->policy()->summarize(['schema' => ['queryEntities' => 'none', 'mutationEntities' => 'none']]),
        );
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
        $this->assertStringContainsString('workspaces.object / delete', $problems[0]);
        $this->assertStringNotContainsString('/ update', $problems[0]);
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

        $policy = new GraphQlConfigPolicy($configurations, new EventDispatcher());
        $policy->save('orders', ['general' => ['type' => 'webhook']]);
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

    public function testAWorkspaceTheSaveWouldDropIsRefused(): void
    {
        $problems = $this->policy()->problems([
            'general' => ['name' => 'no-such-endpoint'],
            'workspaces' => [
                'object' => [
                    ['cpath' => '/', 'read' => true],
                    ['cpath' => '/no-such-folder', 'read' => true],
                    ['path' => '/', 'read' => true],
                ],
                'asset' => ['not-an-entry'],
                'objects' => [['cpath' => '/', 'read' => true]],
            ],
        ], false);

        $this->assertSame([
            'workspaces.object /no-such-folder: no object exists at this path, and a workspace on a missing '
            . 'path is dropped when the configuration is saved.',
            'workspaces.object entry 2 needs "cpath", the full path of the object it grants.',
            'workspaces.asset entry 0 needs "cpath", the full path of the asset it grants.',
            'workspaces.objects is not a workspace type; use document, asset, object.',
        ], $problems);
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

        (new GraphQlConfigPolicy($configurations, new EventDispatcher()))->save('shop', [
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

    public function testGenericTypesSentAsAListAreRefused(): void
    {
        $problems = $this->policy()->problems([
            'general' => ['name' => 'no-such-endpoint'],
            'schema' => ['specialEntities' => [
                ['name' => 'document', 'readAllowed' => true],
                ['name' => 'asset', 'readAllowed' => true],
            ]],
        ], false);

        $this->assertCount(2, $problems);
        $this->problemContaining('schema.specialEntities.0 needs the generic type\'s name as its key', $problems);
        $this->problemContaining('schema.specialEntities.1 needs the generic type\'s name as its key', $problems);
    }

    public function testABareGenericTypeFlagOrAnUnknownGenericTypeIsRefused(): void
    {
        $problems = $this->policy()->problems([
            'general' => ['name' => 'no-such-endpoint'],
            'schema' => ['specialEntities' => [
                'asset' => true,
                'documents' => ['read' => true],
                'document' => ['read' => true],
            ]],
        ], false);

        $this->assertCount(2, $problems);
        $this->problemContaining('schema.specialEntities.asset needs the generic type\'s name as its key', $problems);
        $this->problemContaining('schema.specialEntities.documents is not a generic type; use document,', $problems);
        $this->assertSame(
            ['schema.specialEntities needs the generic types keyed by name, e.g. {"asset": {"read": true}}.'],
            $this->policy()->problems([
                'general' => ['name' => 'no-such-endpoint'],
                'schema' => ['specialEntities' => 'asset'],
            ], false),
        );
    }

    public function testAGenericTypeAListenerAddsIsProposable(): void
    {
        $events = new EventDispatcher();
        $events->addListener(ConfigEvents::SPECIAL_ENTITIES, static function (SpecialEntitiesEvent $event): void {
            $event->addSpecialSetting(
                new SpecialEntitySetting('vendor_keyed_state', true, false, false, false, false, false, false, false),
            );
        });

        $problems = $this->policy($events)->problems([
            'general' => ['name' => 'no-such-endpoint'],
            'schema' => ['specialEntities' => ['vendor_keyed_state' => ['read' => true]]],
        ], false);

        $this->assertSame([], $problems);
        $this->problemContaining(
            'schema.specialEntities.vendor_keyed_state is not a generic type',
            $this->policy()->problems([
                'general' => ['name' => 'no-such-endpoint'],
                'schema' => ['specialEntities' => ['vendor_keyed_state' => ['read' => true]]],
            ], false),
        );
    }

    public function testAGenericTypeTheEndpointAlreadyStoresIsNotRefused(): void
    {
        // stored while a listener offered it; that listener is gone now
        $this->store(GraphQlConfigPolicy::CONFIG_TYPE, [
            'general' => [],
            'schema' => ['specialEntities' => ['vendor_keyed_state' => ['read' => true]]],
        ]);

        $state = [
            'general' => ['name' => self::STORED, 'description' => 'changed'],
            'schema' => ['specialEntities' => ['vendor_keyed_state' => ['read' => true]]],
        ];
        $this->assertSame([], $this->policy()->problems($state, false));

        $state['schema']['specialEntities']['vendor_other_state'] = ['read' => true];
        $this->assertSame(
            ['schema.specialEntities.vendor_other_state is not a generic type; use document, document_folder, asset, '
                . 'asset_folder, asset_listing, object_folder, translation, translation_listing.'],
            $this->policy()->problems($state, false),
        );
    }

    public function testSavingGenericTypesSentAsAListKeepsTheirNames(): void
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

        (new GraphQlConfigPolicy($configurations, new EventDispatcher()))->save('shop', [
            'schema' => ['specialEntities' => [
                ['name' => 'document', 'readAllowed' => true],
                ['readAllowed' => true],
                'asset' => true,
            ]],
        ]);

        $this->assertSame(
            [[
                'name' => 'document',
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

    private function policy(?EventDispatcher $events = null): GraphQlConfigPolicy
    {
        return new GraphQlConfigPolicy(
            $this->createStub(ConfigurationServiceInterface::class),
            $events ?? new EventDispatcher(),
        );
    }
}
