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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\CoreBundle\DependencyInjection\ContainerAwareInterface;
use Pimcore\Bundle\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

/**
 * @internal
 */
final class DocumentElementType extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ServiceTrait;

    /**
     * @param array $config
     */
    public function __construct(Service $graphQlService, $config = [])
    {
        $this->setGraphQLService($graphQlService);
        parent::__construct($config);
    }

    public function getTypes(): array
    {
        $service = $this->getGraphQlService();
        $supportedTypeNames = $service->getSupportedDocumentElementQueryDataTypes();
        $supportedTypes = [];
        foreach ($supportedTypeNames as $typeName) {
            $type = $service->buildDocumentElementDataQueryType($typeName);
            $supportedTypes[] = $type;
        }

        return $supportedTypes;
    }

    public function resolveType($element, $context, ResolveInfo $info): callable|Deferred|ObjectType|null|string
    {
        $type = $element->getType();
        $service = $this->getGraphQlService();
        $supportedTypes = $service->getSupportedDocumentElementQueryDataTypes();
        if (in_array($type, $supportedTypes)) {
            $queryType = $service->buildDocumentElementDataQueryType($type);

            return $queryType;
        }

        return null;
    }
}
