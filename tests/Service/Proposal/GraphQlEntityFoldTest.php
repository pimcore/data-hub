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

use function array_map;
use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigDocument;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\GraphQlConfigPolicy;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * A GraphQL proposal carries only the entities it changes: one it omits keeps its stored value,
 * one sent as null goes.
 */
class GraphQlEntityFoldTest extends Unit
{
    public function testAnOmittedQueryEntityKeepsItsStoredValue(): void
    {
        $car = $this->entity('Car', ['name', 'color']);

        $state = ConfigDocument::fold(
            $this->stored(),
            ['schema' => ['queryEntities' => ['Car' => $car]]],
            $this->policy(),
        );

        $this->assertSame([
            'Event' => $this->entity('Event', ['title', 'date', 'location']),
            'Car' => $car,
            'Manufacturer' => $this->entity('Manufacturer', ['name']),
        ], $state['schema']['queryEntities']);
        $this->assertSame($this->stored()['schema']['mutationEntities'], $state['schema']['mutationEntities']);
        $this->assertSame($this->stored()['general'], $state['general']);
    }

    public function testAQueryEntitySentAsNullIsRemoved(): void
    {
        $state = ConfigDocument::fold(
            $this->stored(),
            ['schema' => ['queryEntities' => ['Manufacturer' => null]]],
            $this->policy(),
        );

        $this->assertSame([
            'Event' => $this->entity('Event', ['title', 'date', 'location']),
            'Car' => $this->entity('Car', ['name']),
        ], $state['schema']['queryEntities']);
    }

    public function testMutationEntitiesMergeByEntityToo(): void
    {
        $event = $this->entity('Event', ['title']);

        $state = ConfigDocument::fold(
            $this->stored(),
            ['schema' => ['mutationEntities' => ['Event' => $event, 'Car' => null]]],
            $this->policy(),
        );

        $this->assertSame(
            ['Event' => $event, 'Manufacturer' => $this->entity('Manufacturer', ['name'])],
            $state['schema']['mutationEntities'],
        );
        $this->assertSame($this->stored()['schema']['queryEntities'], $state['schema']['queryEntities']);
    }

    public function testAnEntityNothingStoredIsAddedAndANullOneLeavesNoTrace(): void
    {
        $state = ConfigDocument::fold(
            [],
            ['schema' => ['queryEntities' => ['Car' => $this->entity('Car', ['name']), 'Event' => null]]],
            $this->policy(),
        );

        $this->assertSame(['Car' => $this->entity('Car', ['name'])], $state['schema']['queryEntities']);
    }

    public function testOtherSchemaKeysStillReplaceWhole(): void
    {
        $state = ConfigDocument::fold(
            $this->stored(),
            ['schema' => ['specialEntities' => ['asset' => ['read' => true]]]],
            $this->policy(),
        );

        $this->assertSame(['asset' => ['read' => true]], $state['schema']['specialEntities']);
        $this->assertSame($this->stored()['schema']['queryEntities'], $state['schema']['queryEntities']);
    }

    public function testWithoutAPolicyEntitiesReplaceWholeAsBefore(): void
    {
        $car = $this->entity('Car', ['name', 'color']);

        $state = ConfigDocument::fold($this->stored(), ['schema' => ['queryEntities' => ['Car' => $car]]]);

        $this->assertSame(['Car' => $car], $state['schema']['queryEntities']);
    }

    private function policy(): GraphQlConfigPolicy
    {
        return new GraphQlConfigPolicy(
            $this->createStub(ConfigurationServiceInterface::class),
            new EventDispatcher(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function stored(): array
    {
        return [
            'general' => ['active' => true, 'description' => 'shop'],
            'schema' => [
                'queryEntities' => [
                    'Event' => $this->entity('Event', ['title', 'date', 'location']),
                    'Car' => $this->entity('Car', ['name']),
                    'Manufacturer' => $this->entity('Manufacturer', ['name']),
                ],
                'mutationEntities' => [
                    'Event' => $this->entity('Event', ['title', 'date']),
                    'Car' => $this->entity('Car', ['name']),
                    'Manufacturer' => $this->entity('Manufacturer', ['name']),
                ],
                'specialEntities' => ['document' => ['read' => true]],
            ],
        ];
    }

    /**
     * @param list<string> $columns
     *
     * @return array<string, mixed>
     */
    private function entity(string $class, array $columns): array
    {
        return [
            'id' => $class,
            'columnConfig' => [
                'columns' => array_map(
                    static fn (string $column): array => ['attributes' => ['attribute' => $column]],
                    $columns,
                ),
            ],
        ];
    }
}
