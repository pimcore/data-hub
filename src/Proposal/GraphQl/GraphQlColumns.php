<?php

declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Proposal\GraphQl;

use function array_is_list;
use function array_map;
use function array_merge;
use function array_values;
use function implode;
use function in_array;
use function is_array;
use function is_bool;
use function is_string;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\Proposal\ColumnFieldResolver;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigDocument;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\Fieldcollections;
use Pimcore\Model\DataObject\Objectbrick\Definition as ObjectbrickDefinition;
use function sprintf;
use function strtolower;

/**
 * The columns of a GraphQL configuration the schema builder could not build. The schema is built
 * on every request: a column naming no field is dropped from the schema, and an unknown brick or
 * operator fails the build, which answers every client of the endpoint with an error.
 *
 * Query columns are checked for every query entity; mutation columns only where the builder
 * reads them, on an entity that may create or update. A column the endpoint already stores is
 * the endpoint's, not the proposal's, and is not checked again.
 *
 * @internal
 */
final readonly class GraphQlColumns
{
    // the system columns the editor offers; a child column reads one through its getter
    private const array SYSTEM_COLUMNS = [
        'id',
        'fullpath',
        'key',
        'published',
        'creationDate',
        'modificationDate',
        'filename',
        'classname',
        'index',
    ];

    private ColumnFieldResolver $fields;

    public function __construct(
        private Service $graphQl,
    ) {
        $this->fields = new ColumnFieldResolver();
    }

    /**
     * @param array<string, mixed> $state
     * @param array<string, mixed> $stored
     *
     * @return list<string>
     */
    public function problems(array $state, array $stored): array
    {
        $problems = [];
        foreach (['queryEntities' => 'query', 'mutationEntities' => 'mutation'] as $kind => $mode) {
            $entities = is_array($state['schema'][$kind] ?? null) ? $state['schema'][$kind] : [];
            foreach ($entities as $key => $entity) {
                // a misshapen entity or an unknown class has a refusal of its own
                $class = is_array($entity) ? ClassDefinition::getByName((string) $key) : null;
                if ($class === null || ($mode === 'mutation' && !$this->writes($entity))) {
                    continue;
                }

                $problems = array_merge($problems, $this->entityProblems(
                    $mode,
                    $class,
                    $entity['columnConfig'] ?? null,
                    $this->storedColumns($stored['schema'][$kind][$key]['columnConfig']['columns'] ?? null),
                    sprintf('schema.%s.%s', $kind, $key),
                ));
            }
        }

        return $problems;
    }

    /**
     * @param list<mixed> $stored
     *
     * @return list<string>
     */
    private function entityProblems(
        string $mode,
        ClassDefinition $class,
        mixed $config,
        array $stored,
        string $at,
    ): array {
        if ($config === null) {
            return [];
        }

        $columns = is_array($config) ? ($config['columns'] ?? []) : null;
        if (!is_array($columns) || !array_is_list($columns)) {
            return [sprintf('%s.columnConfig needs its columns as a list: {"columns": [...]}.', $at)];
        }

        $problems = [];
        foreach ($columns as $index => $column) {
            if (in_array($this->canonical($column), $stored, true)) {
                continue;
            }

            $problems = array_merge(
                $problems,
                $this->columnProblems(
                    $mode,
                    $class,
                    $column,
                    sprintf('%s.columnConfig.columns.%d', $at, $index),
                    false,
                ),
            );
        }

        return $problems;
    }

    /**
     * @return list<string>
     */
    private function columnProblems(string $mode, ClassDefinition $class, mixed $column, string $at, bool $child): array
    {
        if (!is_array($column) || !is_bool($column['isOperator'] ?? null) || !is_array($column['attributes'] ?? null)) {
            return [sprintf(
                '%s needs "isOperator" (true or false) and "attributes" (an object), the shape the Data Hub '
                . 'editor stores. Read the configuration with get_graphql_config and copy a column from there.',
                $at,
            )];
        }

        return $column['isOperator']
            ? $this->operatorProblems($mode, $class, $column['attributes'], $at, $child)
            : $this->fieldProblems($mode, $class, $column['attributes'], $at, $child);
    }

    /**
     * @param array<mixed> $attributes
     *
     * @return list<string>
     */
    private function fieldProblems(
        string $mode,
        ClassDefinition $class,
        array $attributes,
        string $at,
        bool $child,
    ): array {
        $key = $attributes['attribute'] ?? null;
        $dataType = $attributes['dataType'] ?? null;
        if (!is_string($key) || $key === '' || !is_string($dataType) || $dataType === '') {
            return [sprintf(
                '%s needs "attributes.attribute" (the field name) and "attributes.dataType" (its field type).',
                $at,
            )];
        }

        if ($dataType === 'system') {
            return in_array($key, self::SYSTEM_COLUMNS, true) ? [] : [sprintf(
                '%s: "%s" is not a system column; use one of %s.',
                $at,
                $key,
                implode(', ', self::SYSTEM_COLUMNS),
            )];
        }

        $field = $this->fields->field($class, $key);
        if ($field === null) {
            // the builder serves a block's own fields through the block's entry type
            if ($mode === 'query' && !$child && $this->fields->blockOf($class, $key) !== null) {
                return [];
            }

            return [$this->unknownField($class, $key, $at)];
        }

        // the builder reads a fieldcollections column straight off the class
        if ($mode === 'query' && !$child && $dataType === 'fieldcollections'
            && !$class->getFieldDefinition($key) instanceof Fieldcollections) {
            return [sprintf('%s: "%s" is not a fieldcollections field of class %s.', $at, $key, $class->getName())];
        }

        // a mutation input is built by the column's dataType, which the builder trusts
        if ($mode === 'mutation' && !$this->graphQl->supportsDataObjectMutationDataType($dataType)
            && ($child || $this->graphQl->supportsDataObjectMutationDataType($field->getFieldtype()))) {
            return [sprintf(
                '%s: "%s" is not a field type the GraphQL mutations can write; "%s" is of field type %s.',
                $at,
                $dataType,
                $key,
                $field->getFieldtype(),
            )];
        }

        return [];
    }

    /**
     * @param array<mixed> $attributes
     *
     * @return list<string>
     */
    private function operatorProblems(
        string $mode,
        ClassDefinition $class,
        array $attributes,
        string $at,
        bool $child,
    ): array {
        $name = $attributes['class'] ?? null;
        if (!is_string($name) || $name === '') {
            return [sprintf('%s is an operator and needs "attributes.class", the operator\'s name.', $at)];
        }

        if (!$this->hasOperator($mode, strtolower($name), $child)) {
            return [sprintf('%s: "%s" is not a GraphQL %s operator this installation has.', $at, $name, $mode)];
        }

        $children = $attributes['children'] ?? null;
        if (!is_array($children) || !array_is_list($children)) {
            return [sprintf('%s: the operator needs "attributes.children", a list of columns.', $at)];
        }

        // a mutation operator takes its input type from its first child
        if ($mode === 'mutation' && $children === []) {
            return [sprintf('%s: the mutation operator "%s" needs a child column.', $at, $name)];
        }

        $problems = [];
        foreach ($children as $index => $grandchild) {
            $problems = array_merge($problems, $this->columnProblems(
                $mode,
                $class,
                $grandchild,
                sprintf('%s.attributes.children.%d', $at, $index),
                true,
            ));
        }

        return $problems;
    }

    /**
     * The generators the builder looks the operator up in: the schema's field config for a column
     * of its own, the operator itself when a query resolves, the input type for a mutation.
     */
    private function hasOperator(string $mode, string $type, bool $child): bool
    {
        if ($mode === 'query') {
            return $this->graphQl->getQueryOperatorFactories()->has($type) && ($child
                || $this->graphQl->getQueryTypeGeneratorFactories()->has('typegenerator_queryoperator_' . $type));
        }

        return $this->graphQl->getDataObjectMutationTypeGeneratorFactories()->has(
            'typegenerator_dataobjectmutationoperator_' . $type
        ) && ($child || $this->graphQl->getDataObjectMutationOperatorFactories()->has($type));
    }

    private function unknownField(ClassDefinition $class, string $key, string $at): string
    {
        $brickType = $this->fields->brickType($key);
        if ($brickType !== null && ObjectbrickDefinition::getByKey($brickType) === null) {
            return sprintf(
                '%s: "%s" names the brick "%s", which this installation does not have.',
                $at,
                $key,
                $brickType,
            );
        }

        if ($brickType !== null) {
            return sprintf(
                '%s: "%s" is not a field of the brick "%s" in an objectbricks field of class %s.',
                $at,
                $key,
                $brickType,
                $class->getName(),
            );
        }

        return sprintf('%s: "%s" is not a field of class %s or of its localized fields.', $at, $key, $class->getName());
    }

    /**
     * The builder reads the columns only where the entity may create or update.
     *
     * @param array<mixed> $entity
     */
    private function writes(array $entity): bool
    {
        return (bool) ($entity['create'] ?? false) || (bool) ($entity['update'] ?? false);
    }

    /**
     * @return list<mixed>
     */
    private function storedColumns(mixed $columns): array
    {
        return is_array($columns) ? array_map($this->canonical(...), array_values($columns)) : [];
    }

    // a map's key order is no change; a looser comparison would let `true` pass for any stored column
    private function canonical(mixed $column): mixed
    {
        return is_array($column) ? ConfigDocument::normalized($column) : $column;
    }
}
