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

namespace Pimcore\Bundle\DataHubBundle\DependencyInjection;

use Pimcore\Bundle\CoreBundle\DependencyInjection\ConfigurationHelper;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class PimcoreDataHubExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('pimcore_data_hub', $config);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('config.yml');
    }

    public function prepend(ContainerBuilder $container)
    {
        if ($container->hasExtension('doctrine_migrations')) {
            $loader = new YamlFileLoader(
                $container,
                new FileLocator(__DIR__ . '/../Resources/config')
            );

            $loader->load('doctrine_migrations.yml');
        }

        $configDir = PIMCORE_CONFIGURATION_DIRECTORY . '/data_hub';

        if (\Pimcore\Version::getMajorVersion() >= 11) {
            $containerConfig = ConfigurationHelper::getConfigNodeFromSymfonyTree($container, 'pimcore_data_hub');
            $configDir = $containerConfig['config_location']['data_hub']['write_target']['options']['directory'];
        }

        $configLoader = new YamlFileLoader(
            $container,
            new FileLocator($configDir)
        );

        //load datahub configs
        if (\Pimcore\Version::getMajorVersion() >= 11) {
            $configs = ConfigurationHelper::getSymfonyConfigFiles($configDir);
            foreach ($configs as $config) {
                $configLoader->load($config);
            }
        } else {
            $configLocator = new \Pimcore\Bundle\DataHubBundle\Configuration\DatahubConfigLocator();
            foreach ($configLocator->locate('config') as $config) {
                $configLoader->load($config);
            }
        }
    }
}
