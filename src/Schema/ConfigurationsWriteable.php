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
    schema: 'BundleDataHubConfigurationsWriteable',
    title: 'Bundle Data Hub Configurations Writeable',
    required: ['writeable'],
    type: 'object'
)]
final readonly class ConfigurationsWriteable
{
    public function __construct(
        #[Property(
            description: 'Whether new configurations can currently be created in the config store',
            type: 'bool',
            example: true
        )]
        private bool $writeable,
    ) {
    }

    public function isWriteable(): bool
    {
        return $this->writeable;
    }
}
