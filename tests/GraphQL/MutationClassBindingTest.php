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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\Element\ElementInterface;
use ReflectionClass;

class MutationClassBindingTest extends Unit
{
    private const ELEMENT_ID = 987654305;

    /**
     * @var string[]
     */
    private array $registeredKeys = [];

    protected function _after(): void
    {
        foreach ($this->registeredKeys as $key) {
            RuntimeCache::set($key, null);
        }
        $this->registeredKeys = [];
    }

    /**
     * MutationType is final on this line, so the element lookup cannot be stubbed by a subclass.
     * The element is registered in the runtime cache instead, which DataObject::getById() consults first.
     */
    private function buildMutationType(?ElementInterface $element): MutationType
    {
        if ($element !== null) {
            $key = 'object_' . self::ELEMENT_ID;
            RuntimeCache::set($key, $element);
            $this->registeredKeys[] = $key;
        }

        return (new ReflectionClass(MutationType::class))->newInstanceWithoutConstructor();
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

        return $resolver(null, ['id' => self::ELEMENT_ID, 'published' => false], [], null);
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

        $result = $resolver(null, ['id' => self::ELEMENT_ID, 'published' => false], [], null);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Unknown id or fullpath', $result['message']);
    }

    public function testUpdateAcceptsPreBoundObjectOfClass(): void
    {
        $object = $this->concrete('product', 1);
        $object->method('getId')->willReturn(self::ELEMENT_ID);
        $type = $this->buildMutationType(null);
        $resolver = $type->getUpdateObjectResolver([], null, $object, true, $this->productClass());

        $result = $resolver(null, ['id' => self::ELEMENT_ID, 'published' => false], [], null);
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
        $object->method('getId')->willReturn(self::ELEMENT_ID);
        $result = $this->invokeUpdate($object);
        $this->assertTrue($result['success']);
    }
}
