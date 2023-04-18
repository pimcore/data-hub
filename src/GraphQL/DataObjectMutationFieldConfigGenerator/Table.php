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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationFieldConfigGenerator;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class Table extends Base
{
    /** {@inheritdoc } */
    public function getGraphQlMutationFieldConfig($nodeDef, $class, $container = null, $params = [])
    {
        $fieldName = $nodeDef['attributes']['attribute'];
        $tableDef = $class->getFieldDefinition($fieldName);
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

        $rowInput = new InputObjectType([
            'name' => 'RowInput',
            'fields' => $inputItems
        ]);

        $inputType = new InputObjectType([
            'name' => 'TableInput',
            'fields' => [
                'replace' => [
                    'type' => Type::boolean(),
                    'description' => 'if true then the entire table will be overwritten'
                ],
                'rows' => [
                    'type' => Type::listOf($rowInput)
                ]
            ]
        ]);

        return [
            'arg' => $inputType,
            'processor' => [$processor, 'process']
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
