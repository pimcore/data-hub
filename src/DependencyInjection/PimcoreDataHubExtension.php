<?php

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

use Exception;
use Pimcore\Config\LocationAwareConfigRepository;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @internal
 */
final class PimcoreDataHubExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('pimcore_data_hub', $config);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('config.yml');
        $loader->load('studio_backend.yaml');
    }

    /**
     * @throws Exception
     */
    public function prepend(ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        if ($container->hasExtension('doctrine_migrations')) {
            $loader->load('doctrine_migrations.yml');
        }

        $loader->load('pimcore/studio_backend.yaml');
        $loader->load('studio_ui.yaml');

        LocationAwareConfigRepository::loadSymfonyConfigFiles(
            $container,
            'pimcore_data_hub',
            'data_hub'
        );
    }
}
