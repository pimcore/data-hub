<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator;

use Pimcore\Model\DataObject\ClassDefinition\Data;

class Table extends AbstractTable
{
    /**
     * @param Data|Data\Table $fieldDefinition
     * @return array
     */
    function getTableColumnKeys(Data $fieldDefinition): array
    {
        $numCols = (int) $fieldDefinition->getCols();
        if ($numCols === 0) {
            return [];
        }

        $columns = [];
        if ($fieldDefinition->isColumnConfigActivated()) {
            foreach ($fieldDefinition->getColumnConfig() as $columnConfig) {
                $columns[] = $columnConfig['key'];
            }
            return $columns;
        }

        foreach (range(0, $fieldDefinition->getCols() - 1) as $i) {
            $columns[] = 'col' . $i;
        }
        return $columns;
    }
}
