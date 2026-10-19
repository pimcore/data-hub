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

        // the input types are the same for every table with this many columns, so they are shared instead of
        // registering a second type of the same name (e.g. for the same brick in several objectbricks fields)
        $cacheKey = 'graphql_table_input_' . $numCols;

        if (!RuntimeCache::isRegistered($cacheKey)) {
            $rowInput = new InputObjectType([
                'name' => 'RowInput',
                'fields' => $inputItems,
            ]);

            RuntimeCache::set($cacheKey, new InputObjectType([
                'name' => 'TableInput',
                'fields' => [
                    'replace' => [
                        'type' => Type::boolean(),
                        'description' => 'if true then the entire table will be overwritten',
                    ],
                    'rows' => [
                        'type' => Type::listOf($rowInput),
                    ],
                ],
            ]));
        }

        $inputType = RuntimeCache::get($cacheKey);

        return [
            'arg' => $inputType,
            'processor' => [$processor, 'process'],
        ];
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
