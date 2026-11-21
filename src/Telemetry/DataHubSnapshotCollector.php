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

use function addcslashes;
use function array_sum;
use Exception;
use function is_numeric;
use Pimcore\Telemetry\Snapshot\SnapshotCollectorInterface;
use Pimcore\Telemetry\Snapshot\SnapshotQueryRunner;
use function preg_replace;
use function strtolower;

/**
 * The `datahub.*` snapshot namespace: how Data Hub is set up, beyond the yes/no in `usage.datahub`.
 *
 * The interesting line is `config_count_by_type`. "Which integration style do customers actually use -
 * GraphQL, REST, webhooks, scheduled file export?" cannot be answered from a bundle-active flag,
 * because all of them ship inside the same family and one instance can run several at once. Counting
 * active configurations per adapter type answers it with one structural read and no new events.
 *
 * Content-never: adapter types are Pimcore's own identifiers and are allow-listed by
 * {@see DataHubConfigurationUsage} - configuration names, endpoints, API keys and workspace rules never
 * leave. Counts are small structural integers, reported raw for the same reason
 * `datamodel.class_count` is, not because they escaped the bucketing rule (that governs element
 * volumes, which these are not).
 *
 * Emits no configuration counts when the configuration store cannot be read, so an unreachable store
 * never looks like an instance with zero configurations.
 *
 * `error_log_count_24h_by_type` adds the operational side: how many errors each Data Hub package wrote
 * to the Application Logger in the last 24 hours, read by the fixed component prefix each package logs
 * with (a private list here - the satellites' constants are not a dependency Data Hub has). Zeros are
 * reported, the table being absent (the logger bundle is optional) leaves the map out. The two reads are
 * independent probes.
 *
 * @internal
 */
final readonly class DataHubSnapshotCollector implements SnapshotCollectorInterface
{
    private const SCHEMA_VERSION = 1;

    /**
     * Snapshot key => the component prefix the package logs with.
     */
    private const ERROR_LOG_COMPONENTS = [
        'data_importer' => 'DATA-IMPORTER ',
        'file_export' => 'FileExport :: ',
        'productsup' => 'ProductSup :: ',
        'webhooks' => 'WEBHOOKS ',
    ];

    private const ERROR_PRIORITIES = ['error', 'critical', 'alert', 'emergency'];

    public function __construct(
        private DataHubConfigurationUsage $configurationUsage,
        private SnapshotQueryRunner $queryRunner,
    ) {
    }

    public function getNamespace(): string
    {
        return 'datahub';
    }

    public function collect(): array
    {
        $metrics = $this->configurationMetrics();
        $errors = $this->errorLogCounts24h();

        if ($errors !== null) {
            $metrics['schema_version'] = self::SCHEMA_VERSION;
            $metrics['error_log_count_24h_by_type'] = $errors;
        }

        return $metrics;
    }

    /**
     * @return array<string, mixed> empty when the configuration store cannot be read
     */
    private function configurationMetrics(): array
    {
        $active = $this->configurationUsage->activeCountsByType();
        $total = $this->configurationUsage->totalCountsByType();

        if ($active === null || $total === null) {
            return [];
        }

        return [
            'schema_version' => self::SCHEMA_VERSION,
            'config_count' => array_sum($total),
            'active_config_count' => array_sum($active),
            // The Q5 answer: adapter type -> number of active configurations of that type.
            'config_count_by_type' => $active,
        ] + $this->flatCountsByType($active);
    }

    /**
     * The same per-type numbers again, as one flat property each.
     *
     * The map above is the readable form, but analytics cannot chart it: PostHog only indexes scalar
     * properties, so an object-valued property can be stored and read back yet never appears in a
     * breakdown or aggregation - the adapter mix would be reachable only by writing SQL. A flat numeric
     * property per type is chartable directly, which is the difference between a question the product
     * team can answer themselves and one that needs an engineer.
     *
     * Every known type is emitted, including the zeros. An absent property is null rather than zero, and
     * an average that silently skips the instances not using an adapter overstates that adapter's
     * typical footprint.
     *
     * @param array<string, int> $active
     *
     * @return array<string, int>
     */
    private function flatCountsByType(array $active): array
    {
        $flat = [];

        foreach (DataHubConfigurationUsage::reportableTypes() as $type) {
            $flat['config_count_' . $this->propertySuffix($type)] = $active[$type] ?? 0;
        }

        return $flat;
    }

    /**
     * @return array<string, int>|null the four counts, or null when the log table cannot be read
     */
    private function errorLogCounts24h(): ?array
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->queryRunner->quoteIdentifier('application_logs')
            . ' WHERE ' . $this->queryRunner->quoteIdentifier('component') . ' LIKE ?'
            . ' AND ' . $this->queryRunner->quoteIdentifier('priority') . ' IN (?, ?, ?, ?)'
            . ' AND ' . $this->queryRunner->quoteIdentifier('timestamp') . ' >= NOW() - INTERVAL 1 DAY';
        $counts = [];

        foreach (self::ERROR_LOG_COMPONENTS as $key => $prefix) {
            try {
                $count = $this->queryRunner->fetchOne(
                    $sql,
                    [addcslashes($prefix, '%_') . '%', ...self::ERROR_PRIORITIES],
                );
            } catch (Exception) {
                return null;
            }

            $counts[$key] = is_numeric($count) ? (int) $count : 0;
        }

        return $counts;
    }

    /**
     * `simpleRest` -> `simple_rest`, so the property names read like every other snapshot key.
     */
    private function propertySuffix(string $type): string
    {
        return strtolower((string) preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $type));
    }
}
