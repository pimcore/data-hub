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
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\GraphQlWriteGrants;

class GraphQlWriteGrantsTest extends Unit
{
    private const array COLUMNS = ['columns' => [['attributes' => ['attribute' => 'model']]]];

    private const array WIDER = ['columns' => [
        ['attributes' => ['attribute' => 'model']],
        ['attributes' => ['attribute' => 'price']],
    ]];

    public function testChangedInputFieldsOfAnEntityThatMayUpdateAreReported(): void
    {
        $stored = $this->document(['update' => true, 'columnConfig' => self::COLUMNS]);

        $this->assertSame(
            ['schema.mutationEntities.Car columnConfig'],
            (new GraphQlWriteGrants())->changedWriteInputs(
                $this->document(['update' => true, 'columnConfig' => self::WIDER]),
                $stored,
            ),
        );
        $this->assertSame(
            ['schema.mutationEntities.Car columnConfig'],
            (new GraphQlWriteGrants())->changedWriteInputs(
                $this->document(['createAllowed' => 1, 'update' => true]),
                $this->document(['CREATE' => true, 'columnConfig' => self::COLUMNS]),
            ),
        );
    }

    public function testUnchangedInputsOrNoInputWriteAreNotReported(): void
    {
        $grants = new GraphQlWriteGrants();
        $stored = $this->document(['update' => true, 'columnConfig' => self::COLUMNS]);

        // the same fields; read-only or delete-only after the change; an entity not stored yet
        $this->assertSame([], $grants->changedWriteInputs($stored, $stored));
        $this->assertSame([], $grants->changedWriteInputs($this->document(['columnConfig' => self::WIDER]), $stored));
        $this->assertSame([], $grants->changedWriteInputs(
            $this->document(['update' => false, 'delete' => true, 'columnConfig' => self::WIDER]),
            $stored,
        ));
        $this->assertSame([], $grants->changedWriteInputs(
            $this->document(['update' => true, 'columnConfig' => self::WIDER]),
            $this->document(['delete' => true, 'columnConfig' => self::COLUMNS]),
        ));
        $this->assertSame([], $grants->changedWriteInputs(
            $this->document(['update' => true, 'columnConfig' => self::WIDER]),
            [],
        ));
        $this->assertSame([], $grants->changedWriteInputs(['schema' => ['mutationEntities' => ['Car' => 'x']]], $stored));
        $this->assertSame([], $grants->changedWriteInputs(['schema' => ['mutationEntities' => 'none']], $stored));
    }

    /**
     * @param array<string, mixed> $car
     *
     * @return array<string, mixed>
     */
    private function document(array $car): array
    {
        return ['schema' => ['mutationEntities' => ['Car' => ['id' => 'Car', ...$car]]]];
    }
}
