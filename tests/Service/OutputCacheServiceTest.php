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
namespace Pimcore\Bundle\DataHubBundle\Service;

use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\OutputCacheEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class OutputCacheServiceTest extends TestCase
{

    protected $container;
    protected $eventDispatcher;
    protected $request;
    protected $sut;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->method('getParameter')
            ->willReturn(array(
                'graphql' => array(
                    'output_cache_enabled' => true,
                    'output_cache_lifetime' => 25
                )
            ));

        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->eventDispatcher->method('dispatch')
            ->willReturnArgument(1);

        $this->sut = $this->getMockBuilder(OutputCacheService::class)
            ->setConstructorArgs([$this->container, $this->eventDispatcher])
            ->setMethods(['loadFromCache', 'saveToCache'])
            ->getMock();

        $payload = '{"query":"{\n  getProductCategoryListing {\n    edges {\n      node {\n        fullpath\n      }\n    }\n  }\n}","variables":null,"operationName":null}';
        $this->request = Request::create('/api', 'POST', array("apikey" => "super_secret_api_key"), [], [], [], $payload);
        $this->request->headers->set("Content-Type", "application/json");
        $this->request->request->set('clientname', 'test-datahub-config');
    }


    public function testReturnNullWhenItemIsNotCached()
    {
        // Arrange
        $this->sut->method('loadFromCache')->willReturn(null);

        // Act
        $cacheItem = $this->sut->load($this->request);

        // Assert
        $this->assertEquals(null, $cacheItem);
    }


    public function testReturnItemWhenItIsCached()
    {
        // Arrange
        $response = new JsonResponse(['data' => 123]);
        $this->sut->method('loadFromCache')->willReturn($response);

        // Act
        $cacheItem = $this->sut->load($this->request);

        // Assert
        $this->assertEquals($response, $cacheItem);
    }


    public function testSaveItemWhenCacheIsEnabled()
    {
        // Arrange
        $this->sut
            ->expects($this->once())
            ->method('saveToCache');

        $response = new JsonResponse(['data' => 123]);

        // Act
        $this->sut->save($this->request, $response);
    }


    public function testIgnoreSaveWhenCacheIsDisabled()
    {
        // Arrange
        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->method('getParameter')
            ->willReturn(array(
                'graphql' => array(
                    'output_cache_enabled' => false
                )
            ));

        $this->sut = $this->getMockBuilder(OutputCacheService::class)
            ->setConstructorArgs([$this->container, $this->eventDispatcher])
            ->setMethods(['saveToCache'])
            ->getMock();

        $this->sut
            ->expects($this->never())
            ->method('saveToCache');

        $response = new JsonResponse(['data' => 123]);

        // Act
        $this->sut->save($this->request, $response);
    }


    public function testIgnoreLoadWhenCacheIsDisabled()
    {
        // Arrange
        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->method('getParameter')
        ->willReturn(array(
            'graphql' => array(
                'output_cache_enabled' => false
            )
        ));

        $this->sut = $this->getMockBuilder(OutputCacheService::class)
            ->setConstructorArgs([$this->container, $this->eventDispatcher])
            ->setMethods(['loadFromCache'])
            ->getMock();

        $this->sut
            ->expects($this->never())
            ->method('loadFromCache');

        $response = new JsonResponse(['data' => 123]);

        // Act
        $this->sut->save($this->request, $response);
    }


    public function testIgnoreCacheWhenRequestParameterIsPassed()
    {
        // Arrange
        $response = new JsonResponse(['data' => 123]);
        $this->sut->method('loadFromCache')->willReturn($response);
        $this->request->query->set('pimcore_nocache', 'true');
        \Pimcore::inDebugMode(true);

        // Act
        $cacheItem = $this->sut->load($this->request);

        // Assert
        $this->assertEquals(null, $cacheItem);
    }
}
