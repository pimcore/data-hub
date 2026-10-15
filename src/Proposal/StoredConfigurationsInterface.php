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

use Pimcore\Bundle\DataHubBundle\Configuration;

/**
 * The stored configurations of every adapter, as the proposal lanes look them up.
 *
 * @internal
 */
interface StoredConfigurationsInterface
{
    /** the configuration stored under this name, of whatever adapter type; null when there is none */
    public function find(string $name): ?Configuration;

    /**
     * @return list<Configuration>
     */
    public function all(): array;
}
