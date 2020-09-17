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

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Fieldcollection\Definition;

class StructuredTable extends AbstractTable
{
    /**
     * @param Data|Data\StructuredTable $fieldDefinition
     * @return array
     */
    protected function getTableColumns(Data $fieldDefinition): array
    {
        $cols = [];
        foreach ($fieldDefinition->getCols() as $i => $columnConfig) {
            $key = $columnConfig['key'] ?? 'col' . $i;

            switch ($columnConfig['type']) {
                case 'number':
                    $type = Type::float();
                    break;
                case 'bool':
                    $type = Type::boolean();
                    break;
                case 'text':
                default:
                    $type = Type::string();
            }

            $cols[$key] = $type;
        }
        return $cols;
    }
}
