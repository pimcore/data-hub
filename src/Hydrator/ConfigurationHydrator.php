<?php
declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Hydrator;

use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Schema\Configuration as HydratedConfiguration;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\InvalidArgumentException;

/**
 * @internal
 */
final readonly class ConfigurationHydrator implements ConfigurationHydratorInterface
{
    public function hydrate(
        Configuration $config,
        ?string $group = null,
        array $existingChildren = [],
    ): HydratedConfiguration {

        if ($group) {
            return $this->hydrateGroup($config, $existingChildren, $group);
        }

        return $this->hydrateItem($config);
    }
    private function hydrateItem(
        Configuration $config
    ): HydratedConfiguration
    {
        $name = $config->getName();
        $type = $config->getType() ?: 'graphql';

        return new HydratedConfiguration(
            id: $name,
            text: htmlspecialchars($name),
            type: 'config',
            iconCls: 'plugin_pimcore_datahub_icon_' . $type,
            expandable: false,
            leaf: true,
            permissions: [
                'delete' => $config->isAllowed('delete'),
                'update' => $config->isAllowed('update'),
            ],
            adapter: $type,
            writable: $config->isWriteable(),
        );
    }

    /**
     * @param HydratedConfiguration[] $existingChildren
     */
    private function hydrateGroup(
        Configuration $config,
        array $existingChildren,
        string $group
    ): HydratedConfiguration
    {
        return new HydratedConfiguration(
            id: 'group_' . $group,
            text: htmlspecialchars($group),
            type: 'group',
            iconCls: 'pimcore_icon_folder',
            expandable: true,
            leaf: false,
            allowChildren: true,
            group: $group,
            children: [ ... $existingChildren, $this->hydrateItem($config)]
        );
    }
}
