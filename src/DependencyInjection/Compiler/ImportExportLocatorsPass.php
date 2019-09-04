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
declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\DependencyInjection\Compiler;

use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ImportExportLocatorsPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->processQueryTypes($container);
        $this->processMutationTypes($container);


        $this->registerDataTypes(
            $container
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function processQueryTypes(ContainerBuilder $container)
    {
        $graphQLServiceDefinition = $container->getDefinition(Service::class);

        $this->createLocatorForTaggedServices(
            $container,
            $graphQLServiceDefinition,
            'graphql query_typegenerator',
            'pimcore.datahub.graphql.querytypegenerator',
            '$queryTypeGeneratorFactories'
        );

        $this->createLocatorForTaggedServices(
            $container,
            $graphQLServiceDefinition,
            'graphql query operator',
            'pimcore.datahub.graphql.dataobjectqueryoperator_factory',
            '$queryOperatorFactories'
        );

        $this->getSupportedDataTypes(
            'query',
            $container,
            $graphQLServiceDefinition,
            'graphql query_typegenerator',
            'pimcore.datahub.graphql.querytypegenerator'
        );
    }



    /**
     * @param ContainerBuilder $container
     */
    protected function processMutationTypes(ContainerBuilder $container)
    {
        $graphQLServiceDefinition = $container->getDefinition(Service::class);

        $this->createLocatorForTaggedServices(
            $container,
            $graphQLServiceDefinition,
            'graphql mutation_typegenerator',
            'pimcore.datahub.graphql.dataobjectmutationtypegenerator',
            '$mutationTypeGeneratorFactories'
        );

        $this->createLocatorForTaggedServices(
            $container,
            $graphQLServiceDefinition,
            'graphql mutation operator',
            'pimcore.datahub.graphql.mutationoperator_factory',
            '$mutationOperatorFactories'
        );

        $this->getSupportedDataTypes(
            'mutation',
            $container,
            $graphQLServiceDefinition,
            'graphql mutation_typegenerator',
            'pimcore.datahub.graphql.dataobjectmutationtypegenerator'
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param Definition $definition
     * @param string $type
     * @param string $tag
     * @param string $argument
     */
    private function createLocatorForTaggedServices(
        ContainerBuilder $container,
        Definition $definition,
        string $type,
        string $tag,
        string $argument
    ) {
        $resolvers = $container->findTaggedServiceIds($tag);

        $mapping = [];

        foreach ($resolvers as $id => $tagEntries) {
            foreach ($tagEntries as $tagEntry) {
                if (!isset($tagEntry['id'])) {
                    throw new InvalidDefinitionException(sprintf(
                        'The %s "%s" does not define an ID on the "%s" tag.',
                        $type,
                        $id,
                        $tag
                    ));
                }

                $mapping[$tagEntry['id']] = new Reference($id);
            }
        }

        $serviceLocator = new Definition(ServiceLocator::class, [$mapping]);
        $serviceLocator->setPublic(false);
        $serviceLocator->addTag('container.service_locator');

        $definition->setArgument($argument, $serviceLocator);
    }

    /**
     * @param string $operationType
     * @param ContainerBuilder $container
     * @param Definition $definition
     * @param string $type
     * @param string $tag
     */
    private function getSupportedDataTypes(
        $operationType,
        ContainerBuilder $container,
        Definition $definition,
        string $type,
        string $tag
    ) {
        $resolvers = $container->findTaggedServiceIds($tag);

        $mapping = [];

        $needle = 'typegenerator_' . $operationType . 'datatype_';
        $lengthOfNeedle = strlen($needle);

        foreach ($resolvers as $id => $tagEntries) {
            foreach ($tagEntries as $tagEntry) {
                if (!isset($tagEntry['id'])) {
                    throw new InvalidDefinitionException(sprintf(
                        'The %s "%s" does not define an ID on the "%s" tag.',
                        $type,
                        $id,
                        $tag
                    ));
                }

                $idx = strpos($tagEntry['id'], $needle);
                if ($idx === 0) {
                    $typename = substr($tagEntry['id'], $lengthOfNeedle);
                }
                $mapping[$typename] = 1;
            }
        }

        $definition->addMethodCall('setSupported' . ucfirst($operationType) . 'DataTypes', [array_keys($mapping)]);
    }

    /**
     * @param ContainerBuilder $container
     * @param Definition $definition

     */
    private function registerDataTypes(
        ContainerBuilder $container
    ) {
        $graphQLServiceDefinition = $container->getDefinition(Service::class);

        $resolvers = $container->findTaggedServiceIds("pimcore.datahub.graphql.dataobjecttype");

        $dataTypes = [];

        foreach ($resolvers as $id => $tagEntries) {
            foreach ($tagEntries as $tagEntry) {
                $typeDef = $container->getDefinition($id);
                $dataTypes[$tagEntry["id"]] = $typeDef;
            }
        }

        $graphQLServiceDefinition->addMethodCall('registerDataTypes', [$dataTypes]);
    }

}
