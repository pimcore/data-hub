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

namespace Pimcore\Bundle\DataHubBundle\Tests\Service\Telemetry;

use Codeception\Test\Unit;
use Doctrine\DBAL\Connection;
use Pimcore\Bundle\DataHubBundle\Telemetry\DataHubConfigurationUsage;
use Pimcore\Bundle\DataHubBundle\Telemetry\DataHubSnapshotCollector;
use Pimcore\Telemetry\Snapshot\SnapshotQueryRunner;
use RuntimeException;
use function str_contains;
use function str_starts_with;

/**
 * The error counts of the `datahub.*` namespace, over a scripted connection. The configuration counts
 * come from the shared reader and are covered by the live snapshot, not here.
 */
class DataHubSnapshotCollectorTest extends Unit
{
    /**
     * @var list<array{string, list<mixed>}>
     */
    private array $queries = [];

    /**
     * Errors of the last 24 hours per Data Hub package, read from the Application Logger by the fixed
     * component prefix each package logs with. Zeros are reported: a package without errors is a fact.
     */
    public function testReportsErrorsOfTheLastDayPerPackage(): void
    {
        $metrics = $this->collector([
            'DATA-IMPORTER ' => 3,
            'FileExport :: ' => 0,
            'ProductSup :: ' => 1,
            'WEBHOOKS ' => 2,
        ])->collect();

        $this->assertSame(
            ['data_importer' => 3, 'file_export' => 0, 'productsup' => 1, 'webhooks' => 2],
            $metrics['error_log_count_24h_by_type'] ?? null,
        );
        $this->assertSame(2, $metrics['schema_version'] ?? null);
    }

    /**
     * Each count is one bound, time-boxed query: the component prefix as a LIKE pattern, the error
     * priorities, the last day - nothing interpolated, no row content read.
     */
    public function testEachPackageIsOneBoundCountOverTheLastDay(): void
    {
        $this->collector([])->collect();

        $this->assertCount(4, $this->queries);
        foreach ($this->queries as [$sql, $params]) {
            $this->assertStringContainsString('COUNT(*)', $sql);
            $this->assertStringContainsString('component LIKE ?', $sql);
            $this->assertStringContainsString('timestamp >= NOW() - INTERVAL 1 DAY', $sql);
            $this->assertStringNotContainsString('SELECT *', $sql);
            $this->assertTrue(str_contains((string) $params[0], '%'), 'prefix is a LIKE pattern');
            $this->assertSame(['error', 'critical', 'alert', 'emergency'], array_slice($params, 1));
        }
    }

    /**
     * The Application Logger bundle is optional; without its table the counts are unknown and absent.
     */
    public function testAMissingLogTableLeavesTheCountsAbsent(): void
    {
        $metrics = $this->collector(null)->collect();

        $this->assertArrayNotHasKey('error_log_count_24h_by_type', $metrics);
    }

    /**
     * @param array<string, int>|null $countsByPrefix component prefix => count; null makes every query fail
     */
    private function collector(?array $countsByPrefix): DataHubSnapshotCollector
    {
        $this->queries = [];

        $connection = $this->createStub(Connection::class);
        $connection->method('quoteIdentifier')->willReturnArgument(0);
        $connection->method('fetchOne')->willReturnCallback(
            function (string $sql, array $params = []) use ($countsByPrefix): int {
                $this->queries[] = [$sql, $params];
                if ($countsByPrefix === null) {
                    throw new RuntimeException("Table 'application_logs' doesn't exist");
                }
                foreach ($countsByPrefix as $prefix => $count) {
                    if (str_starts_with((string) $params[0], $prefix)) {
                        return $count;
                    }
                }

                return 0;
            }
        );

        return new DataHubSnapshotCollector(
            new DataHubConfigurationUsage(),
            new SnapshotQueryRunner($connection, 0),
        );
    }
}
