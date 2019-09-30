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

namespace Pimcore\Bundle\DataHubBundle\ApiPlatform;

use ApiPlatform\Core\DataProvider\ArrayPaginator;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\RuntimeException;
use Pimcore\Bundle\DataHubBundle\ApiPlatform\Metadata\HubConfigTrait;

class HubCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    use HubConfigTrait;

    /**
     * @var HubExtractor
     */
    private $hubExtractor;

    /**
     * @param HubExtractor $hubExtractor
     */
    public function __construct(HubExtractor $hubExtractor)
    {
        $this->hubExtractor = $hubExtractor;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return strpos($resourceClass, 'pimcore_data_hub_query_') === 0;
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $entity = $this->getEntityName($resourceClass);
        $config = $this->getEntityConfig($resourceClass);

        $listingClass = '\Pimcore\Model\DataObject\\' . $entity . '\Listing';
        $listing = new $listingClass();

        $results = [];

        foreach ($listing as $result) {
            $results[] = $this->hubExtractor->extractConfigFromEntity($config, $result);
        }

        return new ArrayPaginator($results,0, 2);
    }
}
