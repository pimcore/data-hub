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

namespace Pimcore\Bundle\DataHubBundle\Telemetry;

use function array_key_exists;
use function in_array;
use function is_string;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Throwable;

/**
 * The single read behind every Data Hub usage signal, for this bundle and for the four satellites.
 *
 * All five packages share ONE configuration store - Simple REST, webhooks, file export and Productsup
 * are adapter *types* inside Data Hub's own configuration, not separate stores - so one pass over
 * {@see Configuration::getList()} answers "is this adapter configured?" for the whole family. Each
 * satellite asks this service for its own type rather than repeating the read.
 *
 * It goes through the bundle's configuration API on purpose, never a table. Data Hub configurations are
 * location-aware: `pimcore_data_hub.config_location.data_hub` selects between the settings store and
 * Symfony config files under `var/config/data_hub/`, and a third mode disables editing entirely. A
 * probe that queried `plugin_datahub_config` directly would report "no configurations" for every
 * customer on the file target - inventing an adoption gap rather than measuring one.
 *
 * Unresolvable is not the same as empty. When the read fails, every accessor returns null and the
 * callers omit their keys, leaving the signal unknown.
 *
 * @internal
 */
final class DataHubConfigurationUsage
{
    /**
     * Adapter types reported by name. Anything else is counted under {@see self::TYPE_OTHER}: a type is
     * a free-text identifier that a customer or agency adapter can define, and third-party names are
     * kept out of telemetry for the same reason `core.bundles` names only first-party bundles.
     *
     * The list is wider than the five Data Hub packages because the adapter registry is open and other
     * first-party bundles plug into it - Data Importer and Headless Documents both register a type. That
     * is a large part of the value here: the type mix shows which bundles integrate *through* Data Hub,
     * which no bundle-active flag can express.
     *
     * @var list<string>
     */
    private const KNOWN_TYPES = [
        'graphql',
        'simpleRest',
        'ciHub',
        'webhooks',
        'fileExport',
        'productsup',
        'dataImporterDataObject',
        'headlessDocuments',
    ];

    private const TYPE_OTHER = 'other';

    /**
     * Every value {@see self::normalise()} can produce, so a caller can emit one property per type
     * including the zeros rather than only the types this instance happens to use.
     *
     * @return list<string>
     */
    public static function reportableTypes(): array
    {
        return [...self::KNOWN_TYPES, self::TYPE_OTHER];
    }

    /**
     * @var list<array{type: string, active: bool}>|null
     */
    private ?array $configurations = null;

    private bool $loaded = false;

    /**
     * Active configurations per adapter type. Types with no active configuration are absent rather than
     * zero, so the map stays small on instances using one adapter.
     *
     * @return array<string, int>|null null when the configuration store could not be read
     */
    public function activeCountsByType(): ?array
    {
        return $this->countsByType(true);
    }

    /**
     * @return array<string, int>|null null when the configuration store could not be read
     */
    public function totalCountsByType(): ?array
    {
        return $this->countsByType(false);
    }

    /**
     * Whether at least one configuration of any of the given types is active.
     *
     * @param list<string> $types
     *
     * @return bool|null null when the configuration store could not be read
     */
    public function hasActiveOfType(array $types): ?bool
    {
        $counts = $this->activeCountsByType();

        if ($counts === null) {
            return null;
        }

        foreach ($types as $type) {
            if (array_key_exists($this->normalise($type), $counts)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, int>|null
     */
    private function countsByType(bool $activeOnly): ?array
    {
        $configurations = $this->load();

        if ($configurations === null) {
            return null;
        }

        $counts = [];
        foreach ($configurations as $configuration) {
            if ($activeOnly && !$configuration['active']) {
                continue;
            }

            $type = $configuration['type'];
            $counts[$type] = ($counts[$type] ?? 0) + 1;
        }

        return $counts;
    }

    /**
     * Memoised: the snapshot asks up to five times (this bundle plus four satellites) within one run,
     * and each call would otherwise re-read every configuration.
     *
     * @return list<array{type: string, active: bool}>|null
     */
    private function load(): ?array
    {
        if ($this->loaded) {
            return $this->configurations;
        }

        $this->loaded = true;

        try {
            $configurations = [];

            foreach (Configuration::getList() as $configuration) {
                if (!$configuration instanceof Configuration) {
                    continue;
                }

                $configurations[] = [
                    'type' => $this->normalise($configuration->getType()),
                    'active' => (bool) $configuration->isActive(),
                ];
            }

            $this->configurations = $configurations;
        } catch (Throwable) {
            // Could not reach the configuration store - unknown, not empty.
            $this->configurations = null;
        }

        return $this->configurations;
    }

    private function normalise(mixed $type): string
    {
        return is_string($type) && in_array($type, self::KNOWN_TYPES, true) ? $type : self::TYPE_OTHER;
    }
}
