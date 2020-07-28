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
use \Pimcore\Bundle\DataHubBundle\GraphQL\Traits\IdentifierCheckTrait;

use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;

class TestTrait
{
    use IdentifierCheckTrait;

    const BY_ID = "ById";
    const BY_PATH = "ByPath";

    protected function getElementResolver()
    {
        return '\Pimcore\Bundle\DataHubBundle\Tests\GraphQL\Traits\TestTrait';
    }

    protected function getElementById($elementType)
    {
        return $elementType . self::BY_ID;
    }

    protected function getElementByPath($elementType)
    {
        return $elementType . self::BY_PATH;
    }
}

class IdentifierCheckTraitTest extends TestCase
{
    const TRAIT_TO_TEST = '\Pimcore\Bundle\DataHubBundle\GraphQL\Traits\IdentifierCheckTrait';
    const TEST_TYPE = 'TestType';

    public function testThrowingClientSafeExceptionIfTypeIsMissing()
    {
        // Arrange
        $this->expectExceptionMessageMatches('/type expected/');
        $newValueItemValue = array();
        // System under Test
        $sut = $this->getMockForTrait(self::TRAIT_TO_TEST);
        // Act + Assert 
        $sut->getElementByIdOrPath($newValueItemValue);
    }

    public function testThrowingClientSafeExceptionIfBothIdAndFullpathAreMissing()
    {
        // Arrange
        $this->expectExceptionMessageMatches('/Either .* or .* expected/');
        $newValueItemValue = array("type" => self::TEST_TYPE);
        // System under Test
        $sut = $this->getMockForTrait(self::TRAIT_TO_TEST);
        // Act + Assert 
        $sut->getElementByIdOrPath($newValueItemValue);
    }

    public function testIdentifierCheckPrioritizesIdOverFullpath()
    {
        // Arrange
        $newValueItemValue = array(
            "type" => self::TEST_TYPE,
            "id" => 4,
            "fullpath" => "/some/path/withKey"
        );
        // System under Test
        $sut = new TestTrait();
        // Act 
        $result = $sut->getElementByIdOrPath($newValueItemValue);
        // Assert
        $this->assertEquals(self::TEST_TYPE . TestTrait::BY_ID, $result);
    }

    public function testIdentifierCheckGetElementByFullPath()
    {
        // Arrange
        $newValueItemValue = array(
            "type" => self::TEST_TYPE,
            "fullpath" => "/some/path/withKey"
        );
        // System under Test
        $sut = new TestTrait();
        // Act 
        $result = $sut->getElementByIdOrPath($newValueItemValue);
        // Assert
        $this->assertEquals(self::TEST_TYPE . TestTrait::BY_PATH, $result);
    }
}
