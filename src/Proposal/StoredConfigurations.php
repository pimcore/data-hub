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

namespace Pimcore\Bundle\DataHubBundle\Proposal;

use function array_values;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Throwable;

/**
 * @internal
 */
final class StoredConfigurations implements StoredConfigurationsInterface
{
    public function find(string $name): ?Configuration
    {
        try {
            $configuration = Configuration::getByName($name);
        } catch (Throwable) {
            return null;
        }

        return $configuration?->getName() !== null ? $configuration : null;
    }

    public function all(): array
    {
        return array_values(Configuration::getList());
    }
}
