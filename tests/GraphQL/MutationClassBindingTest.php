<?php

declare(strict_types=1);

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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\Element\ElementInterface;
use ReflectionClass;

class MutationClassBindingTest extends Unit
{
    private function buildMutationType(?ElementInterface $element): MutationType
    {
        $type = (new ReflectionClass(MutationTypeStub::class))->newInstanceWithoutConstructor();
        $type->element = $element;

        return $type;
    }

    private function productClass(): ClassDefinition
    {
        $class = new ClassDefinition();
        $class->setId('product');

        return $class;
    }

    private function concrete(string $classId, int $saves): Concrete
    {
        $object = $this->createMock(Concrete::class);
        $object->method('getClassId')->willReturn($classId);
        $object->expects($this->exactly($saves))->method('save');

        return $object;
    }

    private function invokeUpdate(?ElementInterface $element): array
    {
        $type = $this->buildMutationType($element);
        $resolver = $type->getUpdateObjectResolver(
            [],
            null,
            null,
            true,
            $this->productClass()
        );

        return $resolver(null, ['id' => 5, 'published' => false], [], null);
    }

    public function testUpdateRefusesObjectOfOtherClass(): void
    {
        $result = $this->invokeUpdate($this->concrete('category', 0));
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Unknown id or fullpath', $result['message']);
    }

    public function testUpdateRefusesFolder(): void
    {
        $folder = $this->createMock(Folder::class);
        $folder->expects($this->never())->method('save');
        $result = $this->invokeUpdate($folder);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Unknown id or fullpath', $result['message']);
    }

    public function testUpdateRefusesPreBoundObjectOfOtherClass(): void
    {
        $type = $this->buildMutationType(null);
        $resolver = $type->getUpdateObjectResolver(
            [],
            null,
            $this->concrete('category', 0),
            true,
            $this->productClass()
        );

        $result = $resolver(null, ['id' => 5, 'published' => false], [], null);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Unknown id or fullpath', $result['message']);
    }

    public function testUpdateAcceptsPreBoundObjectOfClass(): void
    {
        $object = $this->concrete('product', 1);
        $object->method('getId')->willReturn(5);
        $type = $this->buildMutationType(null);
        $resolver = $type->getUpdateObjectResolver([], null, $object, true, $this->productClass());

        $result = $resolver(null, ['id' => 5, 'published' => false], [], null);
        $this->assertTrue($result['success']);
    }

    public function testUpdateRefusesUnknownElement(): void
    {
        $result = $this->invokeUpdate(null);
        $this->assertFalse($result['success']);
    }

    public function testUpdateStillWorksForObjectOfClass(): void
    {
        $object = $this->concrete('product', 1);
        $object->method('getId')->willReturn(5);
        $result = $this->invokeUpdate($object);
        $this->assertTrue($result['success']);
    }
}

/**
 * @internal
 */
class MutationTypeStub extends MutationType
{
    public ?ElementInterface $element = null;

    protected function getElementById($type, $id)
    {
        return $this->element;
    }

    protected function getElementByPath($type, $fullpath)
    {
        return $this->element;
    }
}
