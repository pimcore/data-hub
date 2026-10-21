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
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\Input;
use Pimcore\Model\DataObject\ClassDefinition\Data\Objectbricks;

class BrickContainerFieldTest extends Unit
{
    public function testParseWithoutContainerField(): void
    {
        $parts = Service::parseObjectBrickFieldName('Engine~power');

        $this->assertSame('Engine', $parts['brickType']);
        $this->assertSame('power', $parts['brickKey']);
        $this->assertNull($parts['brickDescriptor']);
        $this->assertNull($parts['containerField']);
    }

    public function testParseWithContainerField(): void
    {
        $parts = Service::parseObjectBrickFieldName('Engine~power~secondary');

        $this->assertSame('Engine', $parts['brickType']);
        $this->assertSame('power', $parts['brickKey']);
        $this->assertSame('secondary', $parts['containerField']);
    }

    public function testParseWithDescriptorAndContainerField(): void
    {
        $descriptor = '?' . json_encode(['containerKey' => 'Engine', 'brickfield' => 'power']);
        $parts = Service::parseObjectBrickFieldName($descriptor . '~power~secondary');

        $this->assertSame('Engine', $parts['brickType']);
        $this->assertSame('power', $parts['brickKey']);
        $this->assertSame(['containerKey' => 'Engine', 'brickfield' => 'power'], $parts['brickDescriptor']);
        $this->assertSame('secondary', $parts['containerField']);
    }

    public function testParseOfPlainFieldReturnsNothing(): void
    {
        $this->assertSame([], Service::parseObjectBrickFieldName('title'));
    }

    public function testContainerFieldIsUsedWhenItAllowsTheBrick(): void
    {
        $class = $this->buildClass();

        $this->assertSame('secondary', Service::getBrickContainerField($class, 'Engine', 'secondary'));
        $this->assertSame('primary', Service::getBrickContainerField($class, 'Engine', 'primary'));
    }

    public function testWithoutContainerFieldTheFirstAllowingFieldIsUsed(): void
    {
        $class = $this->buildClass();

        $this->assertSame('primary', Service::getBrickContainerField($class, 'Engine'));
        $this->assertSame('primary', Service::getBrickContainerField($class, 'Engine', ''));
    }

    public function testInvalidContainerFieldFallsBackToTheFirstAllowingField(): void
    {
        $class = $this->buildClass();

        // unknown field, a field that is not an objectbricks field, and a field that does not allow the brick
        $this->assertSame('primary', Service::getBrickContainerField($class, 'Engine', 'unknown'));
        $this->assertSame('primary', Service::getBrickContainerField($class, 'Engine', 'title'));
        $this->assertSame('primary', Service::getBrickContainerField($class, 'Engine', 'other'));
        $this->assertSame('other', Service::getBrickContainerField($class, 'Wheels', 'other'));
    }

    public function testBrickWithoutAnyContainerFieldResolvesToNull(): void
    {
        $this->assertNull(Service::getBrickContainerField($this->buildClass(), 'Unused', 'primary'));
    }

    private function buildClass(): ClassDefinition
    {
        $title = new Input();
        $title->setName('title');

        $class = new ClassDefinition();
        $class->setName('BrickContainerFieldTestClass');
        $class->setFieldDefinitions([
            'title' => $title,
            'primary' => $this->buildBricksField('primary', ['Engine']),
            'secondary' => $this->buildBricksField('secondary', ['Engine']),
            'other' => $this->buildBricksField('other', ['Wheels']),
        ]);

        return $class;
    }

    private function buildBricksField(string $name, array $allowedTypes): Objectbricks
    {
        $field = new Objectbricks();
        $field->setName($name);

        // setAllowedTypes() drops brick keys without a stored definition, which this test does not need
        (new \ReflectionProperty(Objectbricks::class, 'allowedTypes'))->setValue($field, $allowedTypes);

        return $field;
    }
}
