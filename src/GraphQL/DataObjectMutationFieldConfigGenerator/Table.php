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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationFieldConfigGenerator;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\DataObject\ClassDefinition\Data;

/**
 * @internal
 */
final class Table extends Base
{
    /** {@inheritdoc } */
    public function getGraphQlMutationFieldConfig($nodeDef, $class, $container = null, $params = [])
    {
        $fieldName = $nodeDef['attributes']['attribute'];
        $tableDef = $this->getGraphQlService()->getObjectFieldHelper()->getFieldDefinitionFromKey($class, $fieldName);
        $inputItems = [];
        $numCols = 0;

        if ($tableDef instanceof Data\Table) {
            $numCols = (int) $tableDef->getCols();
        }

        $this->getProcessors($processors, $tableDef);

        $processor = new \Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor\Table($nodeDef, $processors);
        $processor->setGraphQLService($this->getGraphQlService());

        foreach (range(0, $numCols - 1) as $i) {
            $inputItems['col' . $i] = Type::string();
        }

        $brick = Service::getContainerSelectingBrick($fieldName);

        if ($brick === null) {
            $inputType = $this->createInputType('TableInput', 'RowInput', $inputItems);
        } else {
            // the input types of a brick table which is selected together with its objectbricks field are named after
            // the brick table and shared, so that the same table of several fields does not register them twice
            $suffix = $brick->getKey() . '_' . $tableDef->getName();
            $cacheKey = 'graphql_table_input_' . $suffix;

            if (!RuntimeCache::isRegistered($cacheKey)) {
                RuntimeCache::set(
                    $cacheKey,
                    $this->createInputType('TableInput_' . $suffix, 'RowInput_' . $suffix, $inputItems)
                );
            }

            $inputType = RuntimeCache::get($cacheKey);
        }

        return [
            'arg' => $inputType,
            'processor' => [$processor, 'process'],
        ];
    }

    private function createInputType(string $tableName, string $rowName, array $inputItems): InputObjectType
    {
        $rowInput = new InputObjectType([
            'name' => $rowName,
            'fields' => $inputItems,
        ]);

        return new InputObjectType([
            'name' => $tableName,
            'fields' => [
                'replace' => [
                    'type' => Type::boolean(),
                    'description' => 'if true then the entire table will be overwritten',
                ],
                'rows' => [
                    'type' => Type::listOf($rowInput),
                ],
            ],
        ]);
    }

    public function getProcessors(&$processors, $tableDef)
    {
        $tableHeaderStr = $tableDef->getData();
        $tableHeader = [];

        if (strlen($tableHeaderStr) > 0) {
            $tableHeader = explode('|', $tableHeaderStr);
        }

        $processors = ['tableHeader' => $tableHeader];
    }
}
