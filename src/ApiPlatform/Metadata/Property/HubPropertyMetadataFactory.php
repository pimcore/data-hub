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

use ApiPlatform\Core\Exception\PropertyNotFoundException;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\PropertyMetadata;
use Pimcore\Bundle\DataHubBundle\ApiPlatform\Metadata\HubConfigTrait;
use Symfony\Component\PropertyInfo\Type;

class HubPropertyMetadataFactory implements PropertyMetadataFactoryInterface
{
    use HubConfigTrait;

    /**
     * @var PropertyMetadataFactoryInterface
     */
    private $decorated;

    /**
     * @param PropertyMetadataFactoryInterface $decorated
     */
    public function __construct(PropertyMetadataFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function create(string $resourceClass, string $property, array $options = []): PropertyMetadata
    {
        if (strpos($resourceClass, 'pimcore_data_hub_query_') !== 0) {
            return $this->decorated->create($resourceClass, $property, $options);
        }

        $propertyNames = [];
        $propertyNameCollection = null;
        $config = $this->getEntityConfig($resourceClass);

        foreach ($config['columnConfig']['columns'] as $column) {
            if ($column['attributes']['label'] === $property) {
                return new PropertyMetadata(
                    new Type('string'),
                    $column['attributes']['label'],
                    true,
                    false,
                    '',
                    '',
                    false,
                    $property,
                    '',
                    null,
                    [
                        '_pimcore_data_hub_column' => $column
                    ]
                );
            }
        }

        throw new PropertyNotFoundException('Property with Label not found in Config');
    }
}
