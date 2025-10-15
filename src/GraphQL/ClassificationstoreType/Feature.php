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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\CoreBundle\DependencyInjection\ContainerAwareInterface;
use Pimcore\Bundle\CoreBundle\DependencyInjection\ContainerAwareTrait;

/**
 * @internal
 */
final class Feature extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ServiceTrait;

    /**
     * @param array $config
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'csFeature'])
    {
        $this->setGraphQLService($graphQlService);

        parent::__construct($config);
    }

    /**
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

    public function resolveType($element, $context, ResolveInfo $info): callable|Deferred|ObjectType|null|string
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
