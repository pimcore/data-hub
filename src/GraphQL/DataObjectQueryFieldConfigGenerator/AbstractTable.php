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

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\StringType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Fieldcollection\Definition as FieldcollectionDefinition;
use Pimcore\Model\DataObject\Objectbrick\Definition as ObjectbrickDefinition;

abstract class AbstractTable extends Base
{
    /**
     * @param string $attribute
     * @param Data $fieldDefinition
     * @param ClassDefinition|null $class
     * @param object|null $container
     *
     * @return array
     */
    public function getGraphQlFieldConfig($attribute, Data $fieldDefinition, $class = null, $container = null)
    {
        return $this->enrichConfig($fieldDefinition, $class, $attribute, [
            'name' => $fieldDefinition->getName(),
            'type' => $this->getFieldType($fieldDefinition, $class, $container),
            'resolve' => function ($value, $args, $context = [], ResolveInfo $resolveInfo = null) use ($fieldDefinition, $attribute) {
                $result = Service::resolveValue($value, $fieldDefinition, $attribute, $args);

                // The table has no specific definition of columns, so we cannot have a ObjectType in schema for it.
                // Just return the data JSON encoded
                if ($resolveInfo->returnType instanceof StringType) {
                    return json_encode($result);
                }

                if ($result === null) {
                    return [];
                }

                /** @var \Pimcore\Model\DataObject\Data\StructuredTable $result */
                $rows = ($fieldDefinition instanceof Data\StructuredTable) ? $result->getData() : $result;

                foreach ($rows as &$row) {
                    $row = array_combine(
                        array_map(
                            function ($k) {
                                return is_numeric($k) ? 'col'. $k : $k;
                            },
                            array_keys($row)
                        ),
                        $row
                    );
                }

                return $rows;
            }
        ], $container);
    }

    /**
     * @param Data $fieldDefinition
     * @param ClassDefinition|FieldcollectionDefinition|null $class
     * @param object|null $container
     *
     * @return Type
     */
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        if ($class instanceof ObjectbrickDefinition) {
            $name = 'objectbrick_' . $class->getKey() . '_' . $fieldDefinition->getName();
        } elseif ($class instanceof FieldcollectionDefinition) {
            $name = 'fieldcollection_' . $class->getKey() . '_' . $fieldDefinition->getName();
        } else {
            $name = 'object_' . $class->getName() . '_' . $fieldDefinition->getName();
        }

        $columns = $this->getTableColumns($fieldDefinition);
        if (empty($columns)) {
            return Type::string();
        }

        $type = new ObjectType(
            [
                'name' => $name,
                'fields' => $columns
            ]
        );

        return Type::listOf($type);
    }

    abstract protected function getTableColumns(Data $fieldDefinition): array;
}
