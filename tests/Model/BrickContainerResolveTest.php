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
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\BrickContainerTestEntity;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Service as ClassDefinitionService;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Model\DataObject\Objectbrick\Data\BrickContainerTestBrick;

/**
 * The same brick is allowed in two objectbricks fields (slotA, slotB) of the test class.
 */
class BrickContainerResolveTest extends Unit
{
    private const CLASS_NAME = 'BrickContainerTestEntity';

    private const BRICK_KEY = 'BrickContainerTestBrick';

    private static ?int $objectId = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $resources = __DIR__ . '/../_support/Resources/';

        // the brick has to exist before the class references it, and is mapped to the class afterwards
        $brick = new Objectbrick\Definition();
        $brick->setKey(self::BRICK_KEY);
        ClassDefinitionService::importObjectBrickFromJson(
            $brick,
            file_get_contents($resources . 'brick_BrickContainerTestBrick_import.json'),
            true
        );

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
        ClassDefinition::getByName(self::CLASS_NAME)->save();
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$objectId !== null) {
            BrickContainerTestEntity::getById(self::$objectId, ['force' => true])?->delete();
        }
        ClassDefinition::getByName(self::CLASS_NAME)?->delete();
        Objectbrick\Definition::getByKey(self::BRICK_KEY)?->delete();

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
        $object->getSlotA()->setBrickContainerTestBrick($brickA);

        $brickB = new BrickContainerTestBrick($object);
        $brickB->setLabel('B-value');
        $object->getSlotB()->setBrickContainerTestBrick($brickB);

        $object->save();
        self::$objectId = $object->getId();

        return BrickContainerTestEntity::getById($object->getId(), ['force' => true]);
    }

    public function testValueIsReadFromTheSelectedContainer(): void
    {
        $object = $this->createObject();

        $this->assertSame('A-value', Service::getValueFromObjectBrick($object, self::BRICK_KEY, 'label', null, [], 'slotA'));
        $this->assertSame('B-value', Service::getValueFromObjectBrick($object, self::BRICK_KEY, 'label', null, [], 'slotB'));
    }

    public function testValueIsReadFromTheFirstContainerWithoutSelection(): void
    {
        $object = $this->createObject();

        $this->assertSame('A-value', Service::getValueFromObjectBrick($object, self::BRICK_KEY, 'label'));
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
}
