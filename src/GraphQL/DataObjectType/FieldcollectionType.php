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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\CoreBundle\DependencyInjection\ContainerAwareInterface;
use Pimcore\Bundle\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldcollectionDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Cache\RuntimeCache;

/**
 * @internal
 */
final class FieldcollectionType extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ServiceTrait;

    protected $types;

    /**
     * @param array $config
     */
    public function __construct(Service $graphQlService, $config = [])
    {
        $this->types = $config['types'];
        $this->setGraphQLService($graphQlService);

        parent::__construct($config);
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function resolveType($element, $context, ResolveInfo $info)
    {
        if ($element instanceof FieldcollectionDescriptor) {
            $fcName = $element['__fcType'];
            $fcKey = 'graphql_fieldcollection_' . $fcName;
            $type = RuntimeCache::get($fcKey);

            return $type;
        }

        return null;
    }
}
