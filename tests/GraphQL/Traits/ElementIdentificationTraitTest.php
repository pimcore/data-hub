<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\Traits;

use PHPUnit\Framework\TestCase;
use \Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ElementIdentificationTrait;

use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;

class TestTrait
{
    use ElementIdentificationTrait;

    const BY_ID = "ById";
    const BY_PATH = "ByPath";

    protected function getElementById($elementType)
    {
        return $elementType . self::BY_ID;
    }

    protected function getElementByPath($elementType)
    {
        return $elementType . self::BY_PATH;
    }
}

class ElementIdentificationTraitTest extends TestCase
{
    const TRAIT_TO_TEST = '\Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ElementIdentificationTrait';
    const TEST_TYPE = 'object';

    public function testThrowingClientSafeExceptionIfTypeIsMissing()
    {
        // Arrange
        $this->expectExceptionMessageRegExp('/type expected/');
        $newValueItemValue = array();
        // System under Test
        $sut = $this->getMockForTrait(self::TRAIT_TO_TEST);
        // Act + Assert 
        $sut->getElementByTypeAndIdOrPath($newValueItemValue);
    }

    public function testThrowingClientSafeExceptionIfTypeIsNotSupported()
    {
        // Arrange
        $this->expectExceptionMessageRegExp('/The type .* is not supported/');
        $newValueItemValue = array("type" => "wrong");
        // System under Test
        $sut = $this->getMockForTrait(self::TRAIT_TO_TEST);
        // Act + Assert 
        $sut->getElementByTypeAndIdOrPath($newValueItemValue);
    }

    public function testThrowingClientSafeExceptionIfBothIdAndFullpathAreMissing()
    {
        // Arrange
        $this->expectExceptionMessageRegExp('/either .* or .* expected/');
        $newValueItemValue = array("type" => self::TEST_TYPE);
        // System under Test
        $sut = $this->getMockForTrait(self::TRAIT_TO_TEST);
        // Act + Assert 
        $sut->getElementByTypeAndIdOrPath($newValueItemValue);
    }

    public function testThrowingClientSafeExceptionIfBothIdAndFullpathArePassed()
    {
        // Arrange
        $this->expectExceptionMessage('either id or fullpath expected but not both');
        $newValueItemValue = array(
            "type" => self::TEST_TYPE,
            "id" => 4,
            "fullpath" => "/some/path/withKey"
        );
        // System under Test
        $sut = new TestTrait();
        // Act & Assert
        $sut->getElementByTypeAndIdOrPath($newValueItemValue);
    }

    public function testElementIdentificationGetElementByFullPath()
    {
        // Arrange
        $newValueItemValue = array(
            "type" => self::TEST_TYPE,
            "fullpath" => "/some/path/withKey"
        );
        // System under Test
        $sut = new TestTrait();
        // Act 
        $result = $sut->getElementByTypeAndIdOrPath($newValueItemValue);
        // Assert
        $this->assertEquals(self::TEST_TYPE . TestTrait::BY_PATH, $result);
    }

    public function testElementIdentificationIfTypeCanBePassedAsSeparateArgument()
    {
        // Arrange
        $newValueItemValue = array(
            "fullpath" => "/some/path/withKey"
        );
        // System under Test
        $sut = new TestTrait();
        // Act 
        $result = $sut->getElementByTypeAndIdOrPath($newValueItemValue, self::TEST_TYPE);
        // Assert
        $this->assertEquals(self::TEST_TYPE . TestTrait::BY_PATH, $result);
    }
}
