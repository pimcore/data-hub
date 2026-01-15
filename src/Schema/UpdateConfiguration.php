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

namespace Pimcore\Bundle\DataHubBundle\Schema;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

/**
 * @internal
 */
#[Schema(
    schema: 'BundleDataHubUpdateConfiguration',
    title: 'Bundle Data Hub Update Configuration',
    required: ['configuration', 'modificationDate'],
    type: 'object'
)]
final readonly class UpdateConfiguration
{
    public function __construct(
        #[Property(description: 'Configuration data', type: 'object')]
        private array $configuration,
        #[Property(description: 'Client-side modification date timestamp for conflict detection', type: 'integer', example: 1705075200)]
        private int $modificationDate,
    ) {
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function getModificationDate(): int
    {
        return $this->modificationDate;
    }
}

