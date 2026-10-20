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
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Symfony\Contracts\Service\ResetInterface;

/**
 * @internal
 */
final class Table extends Base implements ResetInterface
{
    /**
     * Input types by column count, shared across all table fields: webonyx/graphql-php rejects
     * a schema containing several distinct type instances with the same name.
     * The first type created per schema build keeps the plain names (`TableInput`, `RowInput`), the
     * others get the column count appended (`TableInput3`). The cache is cleared on `reset()`
     * (kernel.reset) so every schema build starts again with the plain names.
     *
     * @var array<int, InputObjectType>
     */
    private array $inputTypes = [];

    /** {@inheritdoc } */
    public function getGraphQlMutationFieldConfig($nodeDef, $class, $container = null, $params = [])
    {
        $fieldName = $nodeDef['attributes']['attribute'];
        $tableDef = $this->getGraphQlService()->getObjectFieldHelper()->getFieldDefinitionFromKey($class, $fieldName);
        $numCols = 0;

        if ($tableDef instanceof Data\Table) {
            $numCols = (int) $tableDef->getCols();
        }

        $this->getProcessors($processors, $tableDef);

        $processor = new \Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor\Table($nodeDef, $processors);
        $processor->setGraphQLService($this->getGraphQlService());

        $inputType = $this->getInputType($numCols);

        return [
            'arg' => $inputType,
            'processor' => [$processor, 'process'],
        ];
    }

    public function reset(): void
    {
        $this->inputTypes = [];
    }

    private function getInputType(int $numCols): InputObjectType
    {
        if (!isset($this->inputTypes[$numCols])) {
            // The type names depend on the column count; the first table keeps the plain names
            // so schemas with a single table (or equal column counts) are unchanged.
            $suffix = $this->inputTypes === [] ? '' : (string) $numCols;

            $inputItems = [];
            foreach (range(0, $numCols - 1) as $i) {
                $inputItems['col' . $i] = Type::string();
            }

            $rowInput = new InputObjectType([
                'name' => 'RowInput' . $suffix,
                'fields' => $inputItems,
            ]);

            $this->inputTypes[$numCols] = new InputObjectType([
                'name' => 'TableInput' . $suffix,
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

        return $this->inputTypes[$numCols];
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
