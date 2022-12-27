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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PCL
 */
declare(strict_types=1);

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
    public function process(ContainerBuilder $container)
    {
        $this->processGeneralTypes($container);

        $this->processAssetTypes($container);
        $this->processTranslationTypes($container);

        $this->processDataObjectQueryTypes($container);
        $this->processDataObjectMutationTypes($container);
        $this->processDocumentElementQueryTypes($container);
        $this->processDocumentElementMutationTypes($container);
        $this->processCsFeatureQueryTypes($container);

        $this->registerAssetDataTypes($container);
        $this->registerTranslationDataTypes($container);

        $this->registerDataObjectDataTypes($container);

        $this->registerDocumentDataTypes($container);

        $this->registerClassificationStoreDataTypes($container);

        $this->registerPropertyDataTypes($container);
    }

    protected function processGeneralTypes(ContainerBuilder $container)
    {
        $graphQLServiceDefinition = $container->getDefinition(Service::class);

        $this->createLocatorForTaggedServices(
            $container,
            $graphQLServiceDefinition,
            'graphql general',
            'pimcore.datahub.graphql.generaltype_factory',
            '$generalTypeGeneratorFactories'
        );

        $this->buildSupportedGeneralTypes(
            'query',
            $container,
            $graphQLServiceDefinition,
            'graphql general type generator',
            'pimcore.datahub.graphql.generaltypegenerator'
        );
    }

    protected function processAssetTypes(ContainerBuilder $container)
    {
        $graphQLServiceDefinition = $container->getDefinition(Service::class);

        $this->createLocatorForTaggedServices(
            $container,
            $graphQLServiceDefinition,
            'graphql asset',
            'pimcore.datahub.graphql.assettype_factory',
            '$assetTypeGeneratorFactories'
        );
    }

    protected function processTranslationTypes(ContainerBuilder $container)
    {
        $graphQLServiceDefinition = $container->getDefinition(Service::class);

        $this->createLocatorForTaggedServices(
            $container,
            $graphQLServiceDefinition,
            'graphql translation',
            'pimcore.datahub.graphql.translationtype_factory',
            '$translationTypeGeneratorFactories'
        );
    }

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
    private function buildSupportedGeneralTypes(
        $operationType,
        ContainerBuilder $container,
        Definition $definition,
        string $type,
        string $tag
    ) {
        $resolvers = $container->findTaggedServiceIds($tag);

        $mapping = [];

        $needle = 'typegenerator_generaltype_';
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
                    $mapping[$typename] = 1;
                }
            }
        }

        $definition->addMethodCall('setSupportedGeneralTypes', [array_keys($mapping)]);
    }

    protected function processDataObjectQueryTypes(ContainerBuilder $container)
    {
        $graphQLServiceDefinition = $container->getDefinition(Service::class);

        $this->createLocatorForTaggedServices(
            $container,
            $graphQLServiceDefinition,
            'graphql query_dataobjecttypegenerator',
            'pimcore.datahub.graphql.dataobjectquerytypegenerator',
            '$dataObjectQueryTypeGeneratorFactories'
        );

        $this->createLocatorForTaggedServices(
            $container,
            $graphQLServiceDefinition,
            'graphql query operator',
            'pimcore.datahub.graphql.dataobjectqueryoperator_factory',
            '$dataObjectQueryOperatorFactories'
        );

        $this->buildSupportedDataObjectDataTypes(
            'query',
            $container,
            $graphQLServiceDefinition,
            'graphql query_dataobjecttypegenerator',
            'pimcore.datahub.graphql.dataobjectquerytypegenerator'
        );
    }

    /**
     * @param string $operationType
     * @param ContainerBuilder $container
     * @param Definition $definition
     * @param string $type
     * @param string $tag
     */
    private function buildSupportedDataObjectDataTypes(
        $operationType,
        ContainerBuilder $container,
        Definition $definition,
        string $type,
        string $tag
    ) {
        $resolvers = $container->findTaggedServiceIds($tag);

        $mapping = [];

        $needle = 'typegenerator_dataobject' . $operationType . 'datatype_';
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
                    $mapping[$typename] = 1;
                }
            }
        }

        $definition->addMethodCall('setSupportedDataObject' . ucfirst($operationType) . 'DataTypes', [array_keys($mapping)]);
    }

    protected function processDataObjectMutationTypes(ContainerBuilder $container)
    {
        $graphQLServiceDefinition = $container->getDefinition(Service::class);

        $this->createLocatorForTaggedServices(
            $container,
            $graphQLServiceDefinition,
            'graphql dataobject mutation_typegenerator',
            'pimcore.datahub.graphql.dataobjectmutationtypegenerator',
            '$dataObjectMutationTypeGeneratorFactories'
        );

        $this->createLocatorForTaggedServices(
            $container,
            $graphQLServiceDefinition,
            'graphql dataobject mutation operator',
            'pimcore.datahub.graphql.dataobjectmutationoperator_factory',
            '$dataObjectMutationOperatorFactories'
        );

        $this->buildSupportedDataObjectDataTypes(
            'mutation',
            $container,
            $graphQLServiceDefinition,
            'graphql mutation_typegenerator',
            'pimcore.datahub.graphql.dataobjectmutationtypegenerator'
        );
    }

    protected function processDocumentElementMutationTypes(ContainerBuilder $container)
    {
        $graphQLServiceDefinition = $container->getDefinition(Service::class);

        $this->createLocatorForTaggedServices(
            $container,
            $graphQLServiceDefinition,
            'graphql mutation_documentelementtypegenerator',
            'pimcore.datahub.graphql.documentelementmutationtypegenerator',
            '$documentElementMutationTypeGeneratorFactories'
        );

        $this->buildSupportedDocumentElementDataTypes(
            'mutation',
            $container,
            $graphQLServiceDefinition,
            'graphql mutation_documentelementtypegenerator',
            'pimcore.datahub.graphql.documentelementmutationtypegenerator'
        );
    }

    protected function processDocumentElementQueryTypes(ContainerBuilder $container)
    {
        $graphQLServiceDefinition = $container->getDefinition(Service::class);

        $this->createLocatorForTaggedServices(
            $container,
            $graphQLServiceDefinition,
            'graphql query_documentelementtypegenerator',
            'pimcore.datahub.graphql.documentelementquerytypegenerator',
            '$documentElementQueryTypeGeneratorFactories'
        );

        $this->buildSupportedDocumentElementDataTypes(
            'query',
            $container,
            $graphQLServiceDefinition,
            'graphql query_documentelementtypegenerator',
            'pimcore.datahub.graphql.documentelementquerytypegenerator'
        );
    }

    /**
     * @param string $operationType
     * @param ContainerBuilder $container
     * @param Definition $definition
     * @param string $type
     * @param string $tag
     */
    private function buildSupportedDocumentElementDataTypes(
        $operationType,
        ContainerBuilder $container,
        Definition $definition,
        string $type,
        string $tag
    ) {
        $resolvers = $container->findTaggedServiceIds($tag);

        $mapping = [];

        $needle = 'typegenerator_documentelement' . $operationType . 'datatype_';
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
                    $mapping[$typename] = 1;
                }
            }
        }

        $definition->addMethodCall('setSupportedDocumentElement' . ucfirst($operationType) . 'DataTypes', [array_keys($mapping)]);
    }

    protected function processCsFeatureQueryTypes(ContainerBuilder $container)
    {
        $graphQLServiceDefinition = $container->getDefinition(Service::class);

        $this->createLocatorForTaggedServices(
            $container,
            $graphQLServiceDefinition,
            'graphql query_csfeaturetypegenerator',
            'pimcore.datahub.graphql.csfeaturequerytypegenerator',
            '$csFeatureTypeGeneratorFactories'
        );

        $this->buildSupportedCsFeatureDataTypes(
            'query',
            $container,
            $graphQLServiceDefinition,
            'graphql query_csfeaturetypegenerator',
            'pimcore.datahub.graphql.csfeaturequerytypegenerator'
        );
    }

    /**
     * @param string $operationType
     * @param ContainerBuilder $container
     * @param Definition $definition
     * @param string $type
     * @param string $tag
     */
    private function buildSupportedCsFeatureDataTypes(
        $operationType,
        ContainerBuilder $container,
        Definition $definition,
        string $type,
        string $tag
    ) {
        $resolvers = $container->findTaggedServiceIds($tag);

        $mapping = [];

        $needle = 'typegenerator_csfeature' . $operationType . 'datatype_';
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
                    $mapping[$typename] = 1;
                }
            }
        }

        $definition->addMethodCall('setSupportedCsFeature' . ucfirst($operationType) . 'DataTypes', [array_keys($mapping)]);
    }

    private function registerAssetDataTypes(
        ContainerBuilder $container
    ) {
        $this->registerElementTypes($container, 'pimcore.datahub.graphql.assettype', 'registerAssetDataTypes');
    }

    private function registerTranslationDataTypes(ContainerBuilder $container)
    {
        $this->registerElementTypes($container, 'pimcore.datahub.graphql.translationtype', 'registerTranslationDataTypes');
    }

    /**
     * @param ContainerBuilder $container
     * @param string $tag
     * @param string $methodCall
     */
    private function registerElementTypes(
        ContainerBuilder $container,
        $tag,
        $methodCall
    ) {
        $graphQLServiceDefinition = $container->getDefinition(Service::class);

        $resolvers = $container->findTaggedServiceIds($tag);

        $dataTypes = [];

        foreach ($resolvers as $id => $tagEntries) {
            foreach ($tagEntries as $tagEntry) {
                $typeDef = $container->getDefinition($id);
                $dataTypes[$tagEntry['id']] = $typeDef;
            }
        }

        $graphQLServiceDefinition->addMethodCall($methodCall, [$dataTypes]);
    }

    private function registerDataObjectDataTypes(
        ContainerBuilder $container
    ) {
        $this->registerElementTypes($container, 'pimcore.datahub.graphql.dataobjecttype', 'registerDataObjectDataTypes');
    }

    private function registerDocumentDataTypes(
        ContainerBuilder $container
    ) {
        $this->registerElementTypes($container, 'pimcore.datahub.graphql.documenttype', 'registerDocumentDataTypes');
    }

    private function registerClassificationStoreDataTypes(
        ContainerBuilder $container
    ) {
        $this->registerElementTypes($container, 'pimcore.datahub.graphql.cstype', 'registerClassificationStoreDataTypes');
    }

    private function registerPropertyDataTypes(
        ContainerBuilder $container
    ) {
        $this->registerElementTypes($container, 'pimcore.datahub.graphql.propertytype', 'registerPropertyDataTypes');
    }
}
