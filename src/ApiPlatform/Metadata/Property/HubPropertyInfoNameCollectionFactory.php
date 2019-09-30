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

namespace Pimcore\Bundle\DataHubBundle\ApiPlatform\Metadata\Property;

use ApiPlatform\Core\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Property\PropertyNameCollection;
use Pimcore\Bundle\DataHubBundle\ApiPlatform\Metadata\HubConfigTrait;

class HubPropertyInfoNameCollectionFactory implements PropertyNameCollectionFactoryInterface
{
    use HubConfigTrait;

    /**
     * @var PropertyNameCollectionFactoryInterface
     */
    private $decorated;

    /**
     * @param PropertyNameCollectionFactoryInterface $decorated
     */
    public function __construct(PropertyNameCollectionFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function create(string $resourceClass, array $options = []): PropertyNameCollection
    {
        if (strpos($resourceClass, 'pimcore_data_hub_query_') !== 0) {
            return $this->decorated->create($resourceClass, $options);
        }

        $propertyNames = [];
        $propertyNameCollection = null;
        $config = $this->getEntityConfig($resourceClass);

        foreach ($config['columnConfig']['columns'] as $index => $column) {
            $propertyNames[] = (string)$column['attributes']['label'];
        }

        return new PropertyNameCollection(array_values($propertyNames));
    }
}
