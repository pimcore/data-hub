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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class DocumentElementType extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    use ServiceTrait;

    protected $container;

    /**
     * @param Service $graphQlService
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

    /**
     * @inheritdoc
     */
    public function resolveType($element, $context, ResolveInfo $info)
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
