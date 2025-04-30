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
final class StructuredTable extends AbstractTable
{
    protected function getTableColumns(Data $fieldDefinition): array
    {
        $cols = [];
        if ($fieldDefinition instanceof Data\StructuredTable) {
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
        }

        return $cols;
    }
}
