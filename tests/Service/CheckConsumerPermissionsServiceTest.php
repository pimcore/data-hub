<?php

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
namespace Pimcore\Bundle\DataHubBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Symfony\Component\HttpFoundation\Request;

class CheckConsumerPermissionsServiceTest extends TestCase
{
    const CORRECT_API_KEY = "correct_key";

    public function testSecurityCheckFailsWhenNoApiKeyinRequest()
    {
        // Arrange
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getSecurityConfig')
            ->willReturn(array(
                 "method" => Configuration::SECURITYCONFIG_AUTH_APIKEY,
                 "apikey" => self::CORRECT_API_KEY
            ));
        $request = new Request();


        // System under Test
        $sut = new \Pimcore\Bundle\DataHubBundle\Service\CheckConsumerPermissionsService();
        // Act
        $result = $sut->performSecurityCheck($request, $configuration);
        // Assert
        $this->assertFalse($result);
    }

    public function testSecurityCheckFailsWhenInvalidApiKeyInRequest()
    {
        // Arrange
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getSecurityConfig')
            ->willReturn(array(
                "method" => Configuration::SECURITYCONFIG_AUTH_APIKEY,
                "apikey" => self::CORRECT_API_KEY
            ));
        $request = new Request(array("apikey" => "wrong_key"));


        // System under Test
        $sut = new \Pimcore\Bundle\DataHubBundle\Service\CheckConsumerPermissionsService();
        // Act
        $result = $sut->performSecurityCheck($request, $configuration);
        //Assert
        $this->assertFalse($result);
    }

    public function testSecurityCheckPassesWhenCorrectApiKeyInQuery()
    {
        // Arrange
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getSecurityConfig')
            ->willReturn(array(
                "method" => Configuration::SECURITYCONFIG_AUTH_APIKEY,
                "apikey" => self::CORRECT_API_KEY
            ));
        $request = new Request(array("apikey" => self::CORRECT_API_KEY));


        // System under Test
        $sut = new \Pimcore\Bundle\DataHubBundle\Service\CheckConsumerPermissionsService();
        // Act
        $result = $sut->performSecurityCheck($request, $configuration);
        // Assert
        $this->assertTrue($result);
    }

    public function testSecurityCheckPassesWhenCorrectApiKeyInApikeyHeader()
    {
        // Arrange
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getSecurityConfig')
            ->willReturn(array(
                "method" => Configuration::SECURITYCONFIG_AUTH_APIKEY,
                "apikey" => self::CORRECT_API_KEY
            ));
        $request = new Request();
        $request->headers->set("apikey", self::CORRECT_API_KEY);

        // System under Test
        $sut = new \Pimcore\Bundle\DataHubBundle\Service\CheckConsumerPermissionsService();
        // Act
        $result = $sut->performSecurityCheck($request, $configuration);
        // Assert
        $this->assertTrue($result);
    }

    public function testSecurityCheckPassesWhenCorrectXApiKeyInApikeyHeader()
    {
        // Arrange
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getSecurityConfig')
            ->willReturn(array(
                "method" => Configuration::SECURITYCONFIG_AUTH_APIKEY,
                "apikey" => self::CORRECT_API_KEY
            ));
        $request = new Request();
        $request->headers->set("X-API-Key", self::CORRECT_API_KEY);
        // System under Test
        $sut = new \Pimcore\Bundle\DataHubBundle\Service\CheckConsumerPermissionsService();
        // Act
        $result = $sut->performSecurityCheck($request, $configuration);
        // Assert
        $this->assertTrue($result);
    }

    public function testSecurityCheckPrioritizesHeaderOverQueryParam()
    {
        // Arrange
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getSecurityConfig')
            ->willReturn(array(
                "method" => Configuration::SECURITYCONFIG_AUTH_APIKEY,
                "apikey" => self::CORRECT_API_KEY
            ));
        $request = new Request(array("apikey", "wrong_key"));
        $request->headers->set("apikey", self::CORRECT_API_KEY);
        // System under Test
        $sut = new \Pimcore\Bundle\DataHubBundle\Service\CheckConsumerPermissionsService();
        // Act
        $result = $sut->performSecurityCheck($request, $configuration);
        // Assert
        $this->assertTrue($result);
    }
}
