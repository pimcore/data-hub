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
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('pimcore_data_hub');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('graphql')
                    ->children()
                        ->scalarNode('not_allowed_policy')->info('throw exception = 1, return null = 2')->defaultValue(2)->end()
                        ->booleanNode('output_cache_enabled')->info('enables output cache for graphql responses. It is disabled by default')->defaultValue(false)->end()
                        ->integerNode('output_cache_lifetime')->info('output cache in seconds. Default is 30 seconds')->defaultValue(30)->end()
                        ->booleanNode('allow_introspection')->info('enables introspection for graphql. It is enabled by default')->defaultValue(true)->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        $this->addConfigurationsNode($rootNode);
        $this->addSupportedTypes($rootNode);

        /** @var ArrayNodeDefinition $rootNode */
        ConfigurationHelper::addConfigLocationWithWriteTargetNodes(
            $rootNode,
            ['data_hub' => PIMCORE_CONFIGURATION_DIRECTORY . '/data_hub']
        );

        return $treeBuilder;
    }

    private function addConfigurationsNode(ArrayNodeDefinition | NodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('configurations')
                    ->normalizeKeys(false)
                    ->variablePrototype()->end()
                ->end()
            ->end();
    }

    private function addSupportedTypes(ArrayNodeDefinition | NodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('supported_types')
                    ->variablePrototype()->end()
                ->end()
            ->end();
    }
}
