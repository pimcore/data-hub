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
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\ApiPlatform\Metadata;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceNameCollection;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Configuration\Dao;

class HubResourceNameCollectionFactory implements ResourceNameCollectionFactoryInterface
{
    /**
     * @var ResourceNameCollectionFactoryInterface
     */
    protected $decorated;

    /**
     * @param ResourceNameCollectionFactoryInterface $decorated
     */
    public function __construct(ResourceNameCollectionFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function create(): ResourceNameCollection
    {
        $classes = [];

        foreach ($this->decorated->create() as $resourceClass) {
            $classes[$resourceClass] = true;
        }

        $configurationList = Dao::getList();

        /**
         * @var Configuration $config
         */
        foreach ($configurationList as $config) {
            foreach ($config->getConfiguration()['schema']['queryEntities'] as $key => $schema) {
                $classes['pimcore_data_hub_query_' . $config->getName() . '_' . $key] = true;
            }

            foreach ($config->getConfiguration()['schema']['mutationEntities'] as $key => $schema) {
                $classes['pimcore_data_hub_mutation_' . $config->getName() . '_' . $key] = true;
            }
        }

        return new ResourceNameCollection(array_keys($classes));
    }
}
