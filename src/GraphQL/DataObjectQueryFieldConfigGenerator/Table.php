<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator;

use GraphQL\Type\Definition\Type;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class Table extends AbstractTable
{
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
                    $columns[$columnConfig['key']] = Type::string();
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
