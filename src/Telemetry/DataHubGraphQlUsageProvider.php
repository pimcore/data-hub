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

use Pimcore\Telemetry\Usage\BundleUsageProviderInterface;

/**
 * `usage.datahub_graphql` - this bundle's own adapter, the GraphQL endpoint, is used when at least one
 * GraphQL configuration is **active**.
 *
 * Scoped to GraphQL rather than reporting the whole family, so that every package in the family answers
 * the same question about itself and the five booleans are comparable. A family-wide roll-up would have
 * gone true whenever any satellite had a configuration, which reads as "the GraphQL API is in use" and
 * is not - `datahub.active_config_count` already carries the roll-up for anyone who wants it.
 *
 * Deliberately "active", not "exists": a disabled configuration is one somebody built and then turned
 * off, which is the opposite of adoption. The flag self-resets when the last one is deactivated.
 *
 * This is the L3 (configured) signal. How much Data Hub is actually exercised is a different question
 * with a different shape - counts per adapter, request volume - and lives in the `datahub.*` namespace
 * via {@see DataHubSnapshotCollector} rather than being squeezed into this boolean.
 *
 * @internal
 */
final readonly class DataHubGraphQlUsageProvider implements BundleUsageProviderInterface
{
    public function __construct(
        private DataHubConfigurationUsage $configurationUsage,
    ) {
    }

    /**
     * The adapter type this bundle itself ships.
     *
     * @var list<string>
     */
    private const ADAPTER_TYPES = ['graphql'];

    public function getBundleKey(): string
    {
        return 'datahub_graphql';
    }

    public function isUsed(): ?bool
    {
        return $this->configurationUsage->hasActiveOfType(self::ADAPTER_TYPES);
    }
}
