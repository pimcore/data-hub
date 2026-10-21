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

namespace Pimcore\Tests\Model\DataObject;

use Codeception\Test\Unit;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\BrickContainerTestEntity;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\Block;
use Pimcore\Model\DataObject\ClassDefinition\Data\Input;
use Pimcore\Model\DataObject\ClassDefinition\Service as ClassDefinitionService;
use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Model\DataObject\Objectbrick\Data\BrickContainerTestBrick;

/**
 * The same brick is allowed in two objectbricks fields (slotA, slotB) of the test class.
 */
class BrickContainerResolveTest extends Unit
{
    private const CLASS_NAME = 'BrickContainerTestEntity';

    private const BRICK_KEY = 'BrickContainerTestBrick';

    private const OTHER_BRICK_KEY = 'BrickContainerTestOtherBrick';

    private static ?int $objectId = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $resources = __DIR__ . '/../_support/Resources/';

        // the bricks have to exist before the class references them, and are mapped to the class afterwards
        foreach ([self::BRICK_KEY, self::OTHER_BRICK_KEY] as $brickKey) {
            $brick = new Objectbrick\Definition();
            $brick->setKey($brickKey);
            ClassDefinitionService::importObjectBrickFromJson(
                $brick,
                file_get_contents($resources . 'brick_' . $brickKey . '_import.json'),
                true
            );
        }

        $class = new ClassDefinition();
        $class->setName(self::CLASS_NAME);
        $class->setId('BCTE');
        ClassDefinitionService::importClassDefinitionFromJson(
            $class,
            file_get_contents($resources . 'class_BrickContainerTestEntity_import.json'),
            true
        );

        $brick = Objectbrick\Definition::getByKey(self::BRICK_KEY);
        $brick->setClassDefinitions([
            ['classname' => self::CLASS_NAME, 'fieldname' => 'slotA'],
            ['classname' => self::CLASS_NAME, 'fieldname' => 'slotB'],
        ]);
        $brick->save();

        $otherBrick = Objectbrick\Definition::getByKey(self::OTHER_BRICK_KEY);
        $otherBrick->setClassDefinitions([
            ['classname' => self::CLASS_NAME, 'fieldname' => 'slotB'],
        ]);
        $otherBrick->save();

        ClassDefinition::getByName(self::CLASS_NAME)->save();
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$objectId !== null) {
            BrickContainerTestEntity::getById(self::$objectId, ['force' => true])?->delete();
        }
        ClassDefinition::getByName(self::CLASS_NAME)?->delete();
        Objectbrick\Definition::getByKey(self::BRICK_KEY)?->delete();
        Objectbrick\Definition::getByKey(self::OTHER_BRICK_KEY)?->delete();

        parent::tearDownAfterClass();
    }

    private function createObject(): BrickContainerTestEntity
    {
        $object = new BrickContainerTestEntity();
        $object->setParentId(1);
        $object->setKey('brick-container-' . uniqid());
        $object->setPublished(true);

        $containerClassA = 'Pimcore\\Model\\DataObject\\' . self::CLASS_NAME . '\\SlotA';
        $containerClassB = 'Pimcore\\Model\\DataObject\\' . self::CLASS_NAME . '\\SlotB';
        $object->setSlotA(new $containerClassA($object, 'slotA'));
        $object->setSlotB(new $containerClassB($object, 'slotB'));

        $brickA = new BrickContainerTestBrick($object);
        $brickA->setLabel('A-value');
        $brickA->setDetails([['note' => new BlockElement('note', 'input', 'A-note')]]);
        $object->getSlotA()->setBrickContainerTestBrick($brickA);

        $brickB = new BrickContainerTestBrick($object);
        $brickB->setLabel('B-value');
        $brickB->setDetails([['note' => new BlockElement('note', 'input', 'B-note')]]);
        $object->getSlotB()->setBrickContainerTestBrick($brickB);

        $object->save();
        self::$objectId = $object->getId();

        return BrickContainerTestEntity::getById($object->getId(), ['force' => true]);
    }

    private function readLabel(BrickContainerTestEntity $object, ?string $containerField = null): mixed
    {
        return Service::getValueFromObjectBrick($object, self::BRICK_KEY, 'label', null, [], $containerField);
    }

    public function testValueIsReadFromTheSelectedContainer(): void
    {
        $object = $this->createObject();

        $this->assertSame('A-value', $this->readLabel($object, 'slotA'));
        $this->assertSame('B-value', $this->readLabel($object, 'slotB'));
    }

    public function testValueIsReadFromTheFirstContainerWithoutSelection(): void
    {
        $object = $this->createObject();

        $this->assertSame('A-value', $this->readLabel($object));
    }

    public function testValueIsWrittenToTheSelectedContainer(): void
    {
        $object = $this->createObject();

        $setLabel = static fn ($container, $setter) => $container->$setter('changed');

        Service::setValue($object, self::BRICK_KEY . '~label~slotB', $setLabel);

        $this->assertSame('A-value', $object->getSlotA()->getBrickContainerTestBrick()->getLabel());
        $this->assertSame('changed', $object->getSlotB()->getBrickContainerTestBrick()->getLabel());
    }

    public function testValueIsWrittenToTheFirstContainerWithoutSelection(): void
    {
        $object = $this->createObject();

        $setLabel = static fn ($container, $setter) => $container->$setter('changed');

        Service::setValue($object, self::BRICK_KEY . '~label', $setLabel);

        $this->assertSame('changed', $object->getSlotA()->getBrickContainerTestBrick()->getLabel());
        $this->assertSame('B-value', $object->getSlotB()->getBrickContainerTestBrick()->getLabel());
    }

    public function testQueryFieldsOfContainerSelectingColumnsAreNamedAfterTheContainer(): void
    {
        $service = \Pimcore::getContainer()->get(Service::class);
        $fieldHelper = $service->getObjectFieldHelper();
        $class = ClassDefinition::getByName(self::CLASS_NAME);

        $keyOf = static fn (string $attribute) => $fieldHelper->getQueryFieldConfigFromConfig(
            ['isOperator' => false, 'attributes' => ['attribute' => $attribute, 'dataType' => 'input']],
            $class
        )['key'];

        $this->assertSame('label', $keyOf(self::BRICK_KEY . '~label'));
        $this->assertSame('slotA_' . self::BRICK_KEY . '_label', $keyOf(self::BRICK_KEY . '~label~slotA'));
        $this->assertSame('slotB_' . self::BRICK_KEY . '_label', $keyOf(self::BRICK_KEY . '~label~slotB'));
    }

    private function attribute(string $brickKey, string $brickField, string $containerField): string
    {
        return $brickKey . '~' . $brickField . '~' . $containerField;
    }

    private function buildNodeDef(string $attribute, string $dataType): array
    {
        return ['isOperator' => false, 'attributes' => ['attribute' => $attribute, 'dataType' => $dataType]];
    }

    /**
     * @param string[] $attributes
     */
    private function buildQuerySchema(array $attributes, string $dataType): Schema
    {
        $fieldHelper = \Pimcore::getContainer()->get(Service::class)->getObjectFieldHelper();
        $class = ClassDefinition::getByName(self::CLASS_NAME);

        $fields = [];
        foreach ($attributes as $attribute) {
            $query = $fieldHelper->getQueryFieldConfigFromConfig($this->buildNodeDef($attribute, $dataType), $class);
            $fields[$query['key']] = $query['config'];
        }

        $schema = new Schema(['query' => new ObjectType(['name' => 'Query', 'fields' => $fields])]);
        $schema->assertValid();

        return $schema;
    }

    /**
     * @param string[] $attributes
     */
    private function buildMutationSchema(array $attributes): Schema
    {
        $fieldHelper = \Pimcore::getContainer()->get(Service::class)->getObjectFieldHelper();
        $class = ClassDefinition::getByName(self::CLASS_NAME);

        $args = [];
        foreach ($attributes as $attribute) {
            $nodeDef = $this->buildNodeDef($attribute, 'table');
            $mutation = $fieldHelper->getMutationFieldConfigFromConfig($nodeDef, $class);
            $args[$mutation['key']] = ['type' => $mutation['arg']];
        }

        $schema = new Schema([
            'query' => new ObjectType(['name' => 'Query', 'fields' => ['id' => Type::string()]]),
            'mutation' => new ObjectType([
                'name' => 'Mutation',
                'fields' => ['update' => ['type' => Type::string(), 'args' => $args]],
            ]),
        ]);
        $schema->assertValid();

        return $schema;
    }

    public function testSchemaWithTheSameBrickTableAndBlockOfBothContainersIsValid(): void
    {
        $tableSchema = $this->buildQuerySchema([
            $this->attribute(self::BRICK_KEY, 'rows', 'slotA'),
            $this->attribute(self::BRICK_KEY, 'rows', 'slotB'),
        ], 'table');
        $this->assertCount(2, $tableSchema->getQueryType()->getFields());

        $blockSchema = $this->buildQuerySchema([
            $this->attribute(self::BRICK_KEY, 'details', 'slotA'),
            $this->attribute(self::BRICK_KEY, 'details', 'slotB'),
        ], 'block');
        $this->assertCount(2, $blockSchema->getQueryType()->getFields());

        $mutationSchema = $this->buildMutationSchema([
            $this->attribute(self::BRICK_KEY, 'rows', 'slotA'),
            $this->attribute(self::BRICK_KEY, 'rows', 'slotB'),
        ]);
        $this->assertCount(2, $mutationSchema->getMutationType()->getField('update')->args);
    }

    /**
     * @return string[]
     */
    private function queryTypeFieldNames(Schema $schema, string $queryField): array
    {
        $type = $schema->getQueryType()->getField($queryField)->getType()->getWrappedType();

        return array_keys($type->getFields());
    }

    public function testSameNamedTablesAndBlocksOfDifferentBricksKeepTheirOwnShape(): void
    {
        $tableSchema = $this->buildQuerySchema([
            $this->attribute(self::BRICK_KEY, 'rows', 'slotA'),
            $this->attribute(self::OTHER_BRICK_KEY, 'rows', 'slotB'),
        ], 'table');
        $this->assertSame(
            ['col0', 'col1'],
            $this->queryTypeFieldNames($tableSchema, 'slotA_' . self::BRICK_KEY . '_rows')
        );
        $this->assertSame(
            ['col0', 'col1', 'col2'],
            $this->queryTypeFieldNames($tableSchema, 'slotB_' . self::OTHER_BRICK_KEY . '_rows')
        );

        $blockSchema = $this->buildQuerySchema([
            $this->attribute(self::BRICK_KEY, 'details', 'slotA'),
            $this->attribute(self::OTHER_BRICK_KEY, 'details', 'slotB'),
        ], 'block');
        $this->assertSame(
            ['note'],
            $this->queryTypeFieldNames($blockSchema, 'slotA_' . self::BRICK_KEY . '_details')
        );
        $this->assertSame(
            ['other'],
            $this->queryTypeFieldNames($blockSchema, 'slotB_' . self::OTHER_BRICK_KEY . '_details')
        );

        // the mutation input types of tables with a different number of columns must not share a name either
        $mutationSchema = $this->buildMutationSchema([
            $this->attribute(self::BRICK_KEY, 'rows', 'slotA'),
            $this->attribute(self::OTHER_BRICK_KEY, 'rows', 'slotB'),
        ]);
        $this->assertSame(
            ['col0', 'col1'],
            array_keys($mutationSchema->getType('RowInput_' . self::BRICK_KEY . '_rows')->getFields())
        );
        $this->assertSame(
            ['col0', 'col1', 'col2'],
            array_keys($mutationSchema->getType('RowInput_' . self::OTHER_BRICK_KEY . '_rows')->getFields())
        );
    }

    public function testTheSameBrickRelationOfBothContainersSharesItsType(): void
    {
        $fieldHelper = \Pimcore::getContainer()->get(Service::class)->getObjectFieldHelper();
        $class = ClassDefinition::getByName(self::CLASS_NAME);

        $types = [];
        foreach (['slotA', 'slotB'] as $containerField) {
            $nodeDef = $this->buildNodeDef(
                $this->attribute(self::BRICK_KEY, 'related', $containerField),
                'manyToOneRelation'
            );
            $types[] = $fieldHelper->getQueryFieldConfigFromConfig($nodeDef, $class)['config']['type'];
        }

        // a schema must not contain two instances of a type of the same name
        $this->assertSame($types[0], $types[1]);
    }

    public function testBlockEntryTypeOfABrickIsNamedAfterTheBrick(): void
    {
        $schema = $this->buildQuerySchema([$this->attribute(self::BRICK_KEY, 'details', 'slotA')], 'block');

        $type = $schema->getQueryType()->getField('slotA_' . self::BRICK_KEY . '_details')->getType();

        $this->assertSame('block_' . self::BRICK_KEY . '_details_entry', $type->getWrappedType()->name);
    }

    public function testQueryReturnsTheValuesOfTheSelectedContainer(): void
    {
        $object = $this->createObject();

        $schema = $this->buildQuerySchema([
            $this->attribute(self::BRICK_KEY, 'label', 'slotA'),
            $this->attribute(self::BRICK_KEY, 'label', 'slotB'),
            $this->attribute(self::BRICK_KEY, 'details', 'slotA'),
            $this->attribute(self::BRICK_KEY, 'details', 'slotB'),
        ], 'input');

        $result = GraphQL::executeQuery(
            $schema,
            '{ slotA_' . self::BRICK_KEY . '_label slotB_' . self::BRICK_KEY . '_label'
            . ' slotA_' . self::BRICK_KEY . '_details { note } slotB_' . self::BRICK_KEY . '_details { note } }',
            new ElementDescriptor($object)
        )->toArray();

        $this->assertArrayNotHasKey('errors', $result);
        $this->assertSame([
            'slotA_' . self::BRICK_KEY . '_label' => 'A-value',
            'slotB_' . self::BRICK_KEY . '_label' => 'B-value',
            'slotA_' . self::BRICK_KEY . '_details' => [['note' => 'A-note']],
            'slotB_' . self::BRICK_KEY . '_details' => [['note' => 'B-note']],
        ], $result['data']);
    }

    public function testBlockOfAFieldcollectionKeepsItsEntryType(): void
    {
        $input = new Input();
        $input->setName('note');

        $block = new Block();
        $block->setName('details');
        $block->setChildren([$input]);

        $fieldcollection = new Fieldcollection\Definition();
        $fieldcollection->setKey('BrickContainerTestCollection');

        $config = \Pimcore::getContainer()->get(Service::class)->getObjectFieldHelper()
            ->getGraphQlQueryFieldConfig('details', $block, $fieldcollection, null);

        $this->assertSame(
            'block_BrickContainerTestCollection_details_entry',
            $config['type']->getWrappedType()->name
        );
    }
}
