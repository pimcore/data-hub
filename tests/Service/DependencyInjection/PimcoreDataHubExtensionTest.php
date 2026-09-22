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

namespace Pimcore\Bundle\DataHubBundle\DependencyInjection;

use Codeception\Test\Unit;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PimcoreDataHubExtensionTest extends Unit
{
    private const array SUPPORTED_TYPES = [
        'supported_types' => [
            'graphql' => 'Pimcore\Bundle\DataHubBundle',
            'simpleRest' => 'Pimcore\Bundle\DataHubSimpleRestBundle',
        ],
    ];

    public function testGraphQlIsEnabledByDefault(): void
    {
        $container = $this->loadExtension([self::SUPPORTED_TYPES]);

        $this->assertTrue($container->getParameter('pimcore_data_hub')['graphql']['enabled']);
        $this->assertSame(
            ['graphql', 'simpleRest'],
            $container->getParameter('pimcore_data_hub.enabled_adapter_types')
        );
    }

    public function testSwitchingOffGraphQlLeavesOtherAdapterTypesAlone(): void
    {
        $container = $this->loadExtension([self::SUPPORTED_TYPES, ['graphql' => ['enabled' => false]]]);

        $this->assertFalse($container->getParameter('pimcore_data_hub')['graphql']['enabled']);
        $this->assertSame(
            ['simpleRest'],
            $container->getParameter('pimcore_data_hub.enabled_adapter_types')
        );
    }

    public function testTheSupportedTypesKeepReportingWhichBundlesAreInstalled(): void
    {
        $container = $this->loadExtension([self::SUPPORTED_TYPES, ['graphql' => ['enabled' => false]]]);

        $this->assertSame(
            self::SUPPORTED_TYPES['supported_types'],
            $container->getParameter('pimcore_data_hub')['supported_types']
        );
    }

    public function testNoTypeIsEnabledWithoutSupportedTypes(): void
    {
        $container = $this->loadExtension([]);

        $this->assertSame([], $container->getParameter('pimcore_data_hub.enabled_adapter_types'));
    }

    private function loadExtension(array $configs): ContainerBuilder
    {
        $container = new ContainerBuilder();
        (new PimcoreDataHubExtension())->load($configs, $container);

        return $container;
    }
}
