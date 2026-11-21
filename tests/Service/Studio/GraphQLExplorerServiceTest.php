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

namespace Pimcore\Bundle\DataHubBundle\Service\Studio;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\Service\AdapterAvailabilityServiceInterface;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\NotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class GraphQLExplorerServiceTest extends Unit
{
    public function testTheExplorerUrlIsGeneratedForAnEnabledAdapter(): void
    {
        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->willReturn('/explorer/test-config');

        $sut = $this->createService($router, true);

        $this->assertSame('/explorer/test-config', $sut->getExplorerUrl('test-config'));
    }

    public function testTheExplorerUrlIsNotFoundForADisabledAdapter(): void
    {
        $sut = $this->createService($this->createMock(RouterInterface::class), false);

        $this->expectException(NotFoundException::class);

        $sut->getExplorerUrl('test-config');
    }

    public function testTheExplorerIsNotFoundForADisabledAdapter(): void
    {
        $sut = $this->createService($this->createMock(RouterInterface::class), false);

        $this->expectException(NotFoundException::class);

        $sut->generateExplorerResponse('test-config');
    }

    private function createService(RouterInterface $router, bool $enabled): GraphQLExplorerService
    {
        $adapterAvailabilityService = $this->createMock(AdapterAvailabilityServiceInterface::class);
        $adapterAvailabilityService->method('isEnabled')->willReturn($enabled);

        return new GraphQLExplorerService(
            $router,
            $this->createMock(Environment::class),
            $adapterAvailabilityService
        );
    }
}
