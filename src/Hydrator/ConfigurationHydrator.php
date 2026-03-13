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
    ): HydratedConfiguration {
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
            hasStudioColumnConfig: $this->hasStudioColumnConfig($config),
        );
    }

    private function hasStudioColumnConfig(Configuration $config): bool
    {
        $configuration = $config->configuration;

        if (!is_array($configuration)) {
            return false;
        }

        // Rule 1: eventsSchema -> dataObjectClasses -> [*] -> columns
        if ($this->dataObjectClassesHaveColumns($configuration['eventsSchema']['dataObjectClasses'] ?? [])) {
            return true;
        }

        $schema = $configuration['schema'] ?? [];

        if (!is_array($schema)) {
            return false;
        }

        // Rule 2: schema -> columns
        // Rule 3: schema -> dataObjectClasses -> [*] -> columns
        return array_key_exists('columns', $schema)
            || $this->dataObjectClassesHaveColumns($schema['dataObjectClasses'] ?? []);
    }

    private function dataObjectClassesHaveColumns(mixed $dataObjectClasses): bool
    {
        if (!is_array($dataObjectClasses)) {
            return false;
        }

        foreach ($dataObjectClasses as $class) {
            if (is_array($class) && array_key_exists('columns', $class)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param HydratedConfiguration[] $existingChildren
     */
    private function hydrateGroup(
        Configuration $config,
        array $existingChildren,
        string $group
    ): HydratedConfiguration {
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
