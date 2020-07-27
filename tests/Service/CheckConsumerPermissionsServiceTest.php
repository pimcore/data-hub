<?php

namespace Pimcore\Bundle\DataHubBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckConsumerPermissionsServiceTest extends TestCase
{    
    const DATAHUB_METHOD_NAME = "datahub_apikey";
    const CORRECT_API_KEY = "correct_key";

    public function testSecurityCheckFailsWhenNoApiKeyinRequest()
    {   
        // Arrange  
        $configuration = $this->createMock(Configuration::class);  
        $configuration->method('getSecurityConfig')
            ->willReturn(array(
                 "method" => self::DATAHUB_METHOD_NAME,
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
                "method" => self::DATAHUB_METHOD_NAME,
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
                "method" => self::DATAHUB_METHOD_NAME,
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
                "method" => self::DATAHUB_METHOD_NAME,
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
                "method" => self::DATAHUB_METHOD_NAME,
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
                "method" => self::DATAHUB_METHOD_NAME,
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
