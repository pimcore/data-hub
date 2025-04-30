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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator;

use GraphQL\Type\Definition\Type;
use Pimcore\Model\DataObject\ClassDefinition\Data;

/**
 * @internal
 */
final class Table extends AbstractTable
{
    private const NUMERIC_PREFIX = 'col';

    protected function getTableColumns(Data $fieldDefinition): array
    {
        $columns = [];

        if ($fieldDefinition instanceof Data\Table) {
            $numCols = (int) $fieldDefinition->getCols();
            if ($numCols === 0) {
                return [];
            }

            if ($fieldDefinition->isColumnConfigActivated()) {
                foreach ($fieldDefinition->getColumnConfig() as $columnConfig) {
                    $key = $columnConfig['key'];
                    // key must be string, cannot be numeric
                    if (is_numeric($columnConfig['key'])) {
                        $key = self::NUMERIC_PREFIX . $columnConfig['key'];
                    }
                    $columns[$key] = Type::string();
                }

                return $columns;
            }

            foreach (range(0, $fieldDefinition->getCols() - 1) as $i) {
                $columns['col' . $i] = Type::string();
            }
        }

        return $columns;
    }
}
