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
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationFieldConfigGenerator\Fieldcollections;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationFieldConfigGenerator\Table;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\AssetFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DataObjectFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DocumentFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Model\Factory;
use Pimcore\Translation\Translator;
use Psr\Container\ContainerInterface;

class TableTest extends Unit
{
    /** @var string[] */
    private array $registeredCacheKeys = [];

    protected function _after(): void
    {
        foreach ($this->registeredCacheKeys as $key) {
            if (RuntimeCache::isRegistered($key)) {
                RuntimeCache::getInstance()->offsetUnset($key);
            }
        }
        $this->registeredCacheKeys = [];
    }

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
            $config = $generator->getGraphQlMutationFieldConfig(['attributes' => ['attribute' => $field]], $class);
            $types[$field] = $config['arg'];
        }

        // the first table keeps the plain names, so single-table schemas are unchanged
        $this->assertSame('TableInput', $types['foo']->name);
        $this->assertSame('RowInput', $this->getRowInput($types['foo'])->name);
        $this->assertSame('TableInput3', $types['bar']->name);
        $this->assertSame('RowInput3', $this->getRowInput($types['bar'])->name);
        $this->assertSame($types['foo'], $types['baz']);
        $this->assertSame(['col0', 'col1'], array_keys($this->getRowInput($types['foo'])->getFields()));
        $this->assertSame(['col0', 'col1', 'col2'], array_keys($this->getRowInput($types['bar'])->getFields()));

        $this->assertValidSchema(array_values($types));
    }

    public function testResetStartsANewSchemaBuildWithPlainNames(): void
    {
        $class = $this->createClass(['foo' => 3, 'bar' => 2]);
        $generator = new Table($this->createService());

        $three = $generator->getGraphQlMutationFieldConfig(['attributes' => ['attribute' => 'foo']], $class)['arg'];
        $this->assertSame('TableInput', $three->name);

        $generator->reset();

        // the next schema build (e.g. another endpoint in a worker) must not depend on the previous one
        $two = $generator->getGraphQlMutationFieldConfig(['attributes' => ['attribute' => 'bar']], $class)['arg'];
        $this->assertSame('TableInput', $two->name);
        $this->assertSame('RowInput', $this->getRowInput($two)->name);
        $this->assertSame(['col0', 'col1'], array_keys($this->getRowInput($two)->getFields()));
    }

    public function testResetClearsTheParentInputTypeCaches(): void
    {
        MutationType::$typeCache = ['UpdateFooInput' => Type::string()];
        Fieldcollections::$typeCache = ['fieldcollections_foo_input' => Type::string()];

        (new Table($this->createService()))->reset();

        // cached parent inputs reference the table types of the previous build and must not outlive them
        $this->assertSame([], MutationType::$typeCache);
        $this->assertSame([], Fieldcollections::$typeCache);
    }

    public function testContainerAwareBrickTablesShareTypesNamedAfterBrickAndTable(): void
    {
        $this->registerBrick('PmeTableBrick', ['grid' => 2, 'other' => 3]);
        $class = $this->createClass([]);
        $generator = new Table($this->createService());

        $configs = [];
        foreach (['fieldA', 'fieldB'] as $container) {
            $configs[$container] = $generator->getGraphQlMutationFieldConfig(
                ['attributes' => ['attribute' => 'PmeTableBrick~grid~' . $container]],
                $class
            );
        }
        $other = $generator->getGraphQlMutationFieldConfig(
            ['attributes' => ['attribute' => 'PmeTableBrick~other~fieldA']],
            $class
        );

        // the same brick table selected through several objectbricks fields reuses one type instance
        $this->assertSame($configs['fieldA']['arg'], $configs['fieldB']['arg']);
        $this->assertSame('TableInput_PmeTableBrick_grid', $configs['fieldA']['arg']->name);
        $this->assertSame('RowInput_PmeTableBrick_grid', $this->getRowInput($configs['fieldA']['arg'])->name);
        $this->assertSame(['col0', 'col1'], array_keys($this->getRowInput($configs['fieldA']['arg'])->getFields()));

        // another table of the brick gets its own names and columns
        $this->assertSame('TableInput_PmeTableBrick_other', $other['arg']->name);
        $this->assertSame(
            ['col0', 'col1', 'col2'],
            array_keys($this->getRowInput($other['arg'])->getFields())
        );

        $this->assertValidSchema([$configs['fieldA']['arg'], $configs['fieldB']['arg'], $other['arg']]);
    }

    public function testContainerAwareBrickTablesDoNotClashWithPlainTables(): void
    {
        $this->registerBrick('PmeTableBrick', ['grid' => 2]);
        $class = $this->createClass(['foo' => 2]);
        $generator = new Table($this->createService());

        $plain = $generator->getGraphQlMutationFieldConfig(['attributes' => ['attribute' => 'foo']], $class);
        $brick = $generator->getGraphQlMutationFieldConfig(
            ['attributes' => ['attribute' => 'PmeTableBrick~grid~fieldA']],
            $class
        );

        $this->assertSame('TableInput', $plain['arg']->name);
        $this->assertSame('TableInput_PmeTableBrick_grid', $brick['arg']->name);
        $this->assertValidSchema([$plain['arg'], $brick['arg']]);
    }

    /**
     * @param array<string, int> $fields table field name => column count
     */
    private function registerBrick(string $key, array $fields): void
    {
        $brick = new Objectbrick\Definition();
        $brick->setKey($key);
        foreach ($fields as $name => $cols) {
            $table = new Data\Table();
            $table->setName($name);
            $table->setCols($cols);
            $brick->addFieldDefinition($name, $table);
        }

        RuntimeCache::set('objectbrick_' . $key, $brick);
        $this->registeredCacheKeys[] = 'objectbrick_' . $key;
        foreach (array_keys($fields) as $name) {
            // the shared input types live in the runtime cache for the duration of a schema build
            $this->registeredCacheKeys[] = 'graphql_table_input_' . $key . '_' . $name;
        }
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
