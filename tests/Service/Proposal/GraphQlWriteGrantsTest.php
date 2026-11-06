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
    private const string CAR_COLUMNS = 'schema.mutationEntities.Car columnConfig';

    private const array COLUMNS = ['columns' => [['attributes' => ['attribute' => 'model']]]];

    private const array WIDER = ['columns' => [
        ['attributes' => ['attribute' => 'model']],
        ['attributes' => ['attribute' => 'price']],
    ]];

    public function testChangedInputFieldsOfAnEntityThatMayUpdateAreReported(): void
    {
        $stored = $this->document(['update' => true, 'columnConfig' => self::COLUMNS]);

        $this->assertSame(
            [self::CAR_COLUMNS],
            (new GraphQlWriteGrants())->changedWriteInputs(
                $this->document(['update' => true, 'columnConfig' => self::WIDER]),
                $stored,
            ),
        );
        $this->assertSame(
            [self::CAR_COLUMNS],
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
        $wider = $this->document(['update' => true, 'columnConfig' => self::WIDER]);
        $deleteOnly = $this->document(['update' => false, 'delete' => true, 'columnConfig' => self::WIDER]);
        $storedDeleteOnly = $this->document(['delete' => true, 'columnConfig' => self::COLUMNS]);
        $this->assertSame([], $grants->changedWriteInputs($deleteOnly, $stored));
        $this->assertSame([], $grants->changedWriteInputs($wider, $storedDeleteOnly));
        $this->assertSame([], $grants->changedWriteInputs($wider, []));
        $notAnEntity = ['schema' => ['mutationEntities' => ['Car' => 'x']]];
        $this->assertSame([], $grants->changedWriteInputs($notAnEntity, $stored));
        $this->assertSame([], $grants->changedWriteInputs(['schema' => ['mutationEntities' => 'none']], $stored));
    }

    public function testInputFieldsThatDifferOnlyInKeyOrderAreNotReported(): void
    {
        $model = ['attribute' => 'model', 'label' => 'Model'];
        $columns = ['columns' => [['attributes' => $model, 'isOperator' => false]]];
        $reordered = ['columns' => [['isOperator' => false, 'attributes' => array_reverse($model)]]];
        $grants = new GraphQlWriteGrants();

        $this->assertSame(
            [],
            $grants->changedWriteInputs(
                $this->document(['update' => true, 'columnConfig' => $reordered]),
                $this->document(['columnConfig' => $columns, 'update' => true]),
            ),
        );
        // a list's order is the fields' order, so it still counts
        $reversed = ['columns' => array_reverse(self::WIDER['columns'])];
        $this->assertSame(
            [self::CAR_COLUMNS],
            $grants->changedWriteInputs(
                $this->document(['update' => true, 'columnConfig' => $reversed]),
                $this->document(['update' => true, 'columnConfig' => self::WIDER]),
            ),
        );
    }

    public function testInputFieldsThatDifferOnlyInIntegralNumberTypeAreNotReported(): void
    {
        $grants = new GraphQlWriteGrants();
        $stored = $this->document(['update' => true, 'columnConfig' => ['columns' => [['width' => 200]]]]);

        $this->assertSame(
            [],
            $grants->changedWriteInputs(
                $this->document(['update' => true, 'columnConfig' => ['columns' => [['width' => 200.0]]]]),
                $stored,
            ),
        );
        // a different number or a string is still a change
        foreach ([200.5, '200'] as $width) {
            $this->assertSame(
                [self::CAR_COLUMNS],
                $grants->changedWriteInputs(
                    $this->document(['update' => true, 'columnConfig' => ['columns' => [['width' => $width]]]]),
                    $stored,
                ),
            );
        }
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
