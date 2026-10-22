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
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Cache\RuntimeCache;
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
     * (kernel.reset), together with the parents' static input type caches, so every schema build
     * starts again with the plain names.
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

        $brick = Service::getContainerSelectingBrick($fieldName);

        if ($brick === null) {
            $inputType = $this->getInputType($numCols);
        } else {
            // the input types of a brick table which is selected together with its objectbricks field are named after
            // the brick table and shared, so that the same table of several fields does not register them twice
            $suffix = $brick->getKey() . '_' . $tableDef->getName();
            $cacheKey = 'graphql_table_input_' . $suffix;

            if (!RuntimeCache::isRegistered($cacheKey)) {
                RuntimeCache::set(
                    $cacheKey,
                    $this->createInputType(
                        'TableInput_' . $suffix,
                        'RowInput_' . $suffix,
                        $this->getRowFields($numCols)
                    )
                );
            }

            $inputType = RuntimeCache::get($cacheKey);
        }

        return [
            'arg' => $inputType,
            'processor' => [$processor, 'process'],
        ];
    }

    /**
     * @return array<string, Type>
     */
    private function getRowFields(int $numCols): array
    {
        $fields = [];
        foreach (range(0, $numCols - 1) as $i) {
            $fields['col' . $i] = Type::string();
        }

        return $fields;
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

    public function reset(): void
    {
        $this->inputTypes = [];

        // input types cached by the parents embed the table types of the previous schema build; they have to go
        // along with them, otherwise a new build mixes old and new instances of the same type name
        MutationType::$typeCache = [];
        Fieldcollections::$typeCache = [];
    }

    private function getInputType(int $numCols): InputObjectType
    {
        if (!isset($this->inputTypes[$numCols])) {
            // The type names depend on the column count; the first table keeps the plain names
            // so schemas with a single table (or equal column counts) are unchanged.
            $suffix = $this->inputTypes === [] ? '' : (string) $numCols;

            $this->inputTypes[$numCols] = $this->createInputType(
                'TableInput' . $suffix,
                'RowInput' . $suffix,
                $this->getRowFields($numCols)
            );
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
