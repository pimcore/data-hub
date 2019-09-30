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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\ApiPlatform\Metadata;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use Pimcore\Bundle\DataHubBundle\Configuration;

class HubResourceMetadataFactory implements ResourceMetadataFactoryInterface
{
    use HubConfigTrait;

    private $decorated;

    public function __construct(ResourceMetadataFactoryInterface $decorated = null)
    {
        $this->decorated = $decorated;
    }

    public function create(string $resourceClass): ResourceMetadata
    {
        if (strpos($resourceClass, 'pimcore_data_hub_query_') !== 0) {
            return $this->decorated->create($resourceClass);
        }

        $configName = $this->getConfigName($resourceClass);
        $entity = $this->getEntityName($resourceClass);


        $resourceMetadata = new ResourceMetadata(
            $configName . '_' . $entity,
            'Data Hub Query ' . $entity,
            '',
            ['get' => ['method' => 'GET']],
            ['get' => ['method' => 'GET']],
            [
                '_pimcore_data_hub_config_name' => $configName,
                '_pimcore_data_hub_entity_name' => $entity,
                '_pimcore_data_hub_api_config' => $this->getApiConfig($resourceClass),
                '_pimcore_data_hub_entity_config' => $this->getEntityConfig($resourceClass)
            ],
            [],
            []
        );

        return $resourceMetadata->withGraphql(['item_query' => [], 'collection_query' => [], 'delete' => [], 'update' => [], 'create' => []]);
    }
}
