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

namespace Pimcore\Bundle\DataHubBundle\Service;

/**
 * Tells which Data Hub adapter types are available on this system: those whose bundle is installed
 * and which are not switched off in the configuration. An unavailable adapter type is unavailable to
 * every user, including admins.
 *
 * @internal
 */
interface AdapterAvailabilityServiceInterface
{
    public function isEnabled(string $type): bool;

    /**
     * @return string[]
     */
    public function getEnabledTypes(): array;
}
