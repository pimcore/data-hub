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
    schema: 'BundleDataHubUpdateConfigurationResponse',
    title: 'Bundle Data Hub Update Configuration Response',
    required: ['modificationDate'],
    type: 'object'
)]
final readonly class UpdateConfigurationResponse
{
    public function __construct(
        #[Property(description: 'New modification date timestamp', type: 'integer', example: 1705075200)]
        private int $modificationDate,
    ) {
    }

    public function getModificationDate(): int
    {
        return $this->modificationDate;
    }
}

