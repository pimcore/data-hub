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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Feature extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    use ServiceTrait;

    /**
     * @param Service $graphQlService
     * @param array $config
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'csFeature'])
    {
        $this->setGraphQLService($graphQlService);

        parent::__construct($config);
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getTypes(): array
    {
        $service = $this->getGraphQlService();
        $supportedFeatureTypeNames = $service->getSupportedCsFeatureQueryDataTypes();

        $types = [];
        foreach ($supportedFeatureTypeNames as $featureTypeName) {
            $featureType = $service->buildCsFeatureDataQueryType($featureTypeName);
            $types[] = $featureType;
        }

        return $types;
    }

    /**
     * @inheritdoc
     */
    public function resolveType($element, $context, ResolveInfo $info)
    {
        if (!$element instanceof FeatureDescriptor) {
            throw new ClientSafeException('expected feature descriptor');
        }

        $type = $element->getType();

        $service = $this->getGraphQlService();
        $resolvedType = $service->buildCsFeatureDataQueryType($type);

        return $resolvedType;
    }
}
