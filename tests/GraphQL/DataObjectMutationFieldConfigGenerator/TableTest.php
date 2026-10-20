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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\DataObjectMutationFieldConfigGenerator;

use Codeception\Test\Unit;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationFieldConfigGenerator\Table;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\AssetFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DataObjectFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DocumentFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\Factory;
use Pimcore\Translation\Translator;
use Psr\Container\ContainerInterface;

class TableTest extends Unit
{
    /**
     * Regression test for https://github.com/pimcore/data-hub/issues/894:
     * a class with more than one table field (e.g. in several object bricks) must not end up
     * with several distinct "TableInput"/"RowInput" type instances, which webonyx rejects
     * as non-unique named types.
     */
    public function testMultipleTableFieldsWithSameColumnCountShareInputTypes(): void
    {
        $class = $this->createClass(['foo' => 2, 'bar' => 2]);
        $generator = new Table($this->createService());

        $first = $generator->getGraphQlMutationFieldConfig(['attributes' => ['attribute' => 'foo']], $class);
        $second = $generator->getGraphQlMutationFieldConfig(['attributes' => ['attribute' => 'bar']], $class);

        $this->assertSame('TableInput', $first['arg']->name);
        $this->assertSame($first['arg'], $second['arg']);
        $this->assertSame(['col0', 'col1'], array_keys($this->getRowInput($first['arg'])->getFields()));

        $this->assertValidSchema([$first['arg'], $second['arg']]);
    }

    public function testTableFieldsWithDifferentColumnCountsKeepTheirColumns(): void
    {
        $class = $this->createClass(['foo' => 2, 'bar' => 3, 'baz' => 2]);
        $generator = new Table($this->createService());

        $types = [];
        foreach (['foo', 'bar', 'baz'] as $field) {
            $types[$field] = $generator->getGraphQlMutationFieldConfig(['attributes' => ['attribute' => $field]], $class)['arg'];
        }

        // the first table keeps the plain names, so single-table schemas are unchanged
        $this->assertSame('TableInput', $types['foo']->name);
        $this->assertSame('RowInput', $this->getRowInput($types['foo'])->name);
        $this->assertNotSame($types['foo']->name, $types['bar']->name);
        $this->assertSame($types['foo'], $types['baz']);
        $this->assertSame(['col0', 'col1'], array_keys($this->getRowInput($types['foo'])->getFields()));
        $this->assertSame(['col0', 'col1', 'col2'], array_keys($this->getRowInput($types['bar'])->getFields()));

        $this->assertValidSchema(array_values($types));
    }

    /**
     * @param array<string, int> $fields field name => column count
     */
    private function createClass(array $fields): ClassDefinition
    {
        $class = new ClassDefinition();
        foreach ($fields as $name => $cols) {
            $table = new Data\Table();
            $table->setName($name);
            $table->setCols($cols);
            $class->addFieldDefinition($name, $table);
        }

        return $class;
    }

    private function getRowInput(InputObjectType $tableInput): InputObjectType
    {
        $rowsType = $tableInput->getField('rows')->getType();
        $rowInput = Type::getNullableType($rowsType)->getWrappedType();

        $this->assertInstanceOf(InputObjectType::class, $rowInput);

        return $rowInput;
    }

    /**
     * @param InputObjectType[] $inputTypes
     */
    private function assertValidSchema(array $inputTypes): void
    {
        $mutationFields = [];
        foreach ($inputTypes as $i => $inputType) {
            $mutationFields['update' . $i] = [
                'type' => Type::string(),
                'args' => ['input' => $inputType],
            ];
        }

        $schema = new Schema([
            'query' => new ObjectType([
                'name' => 'Query',
                'fields' => ['dummy' => Type::string()],
            ]),
            'mutation' => new ObjectType([
                'name' => 'Mutation',
                'fields' => $mutationFields,
            ]),
        ]);

        $schema->assertValid();
    }

    private function createService(): Service
    {
        $factories = new class() implements ContainerInterface {
            public function get(string $id): mixed
            {
                throw new \LogicException(sprintf('Unexpected factory lookup "%s"', $id));
            }

            public function has(string $id): bool
            {
                return false;
            }
        };

        return new Service(
            new AssetFieldHelper(),
            new DocumentFieldHelper(),
            new DataObjectFieldHelper(),
            $this->createMock(LocaleServiceInterface::class),
            new Factory(),
            $this->createMock(Translator::class),
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
        );
    }
}
