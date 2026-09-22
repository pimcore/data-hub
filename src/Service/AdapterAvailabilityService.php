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

use function in_array;

/**
 * @internal
 */
final readonly class AdapterAvailabilityService implements AdapterAvailabilityServiceInterface
{
    /**
     * @param string[] $enabledTypes resolved by PimcoreDataHubExtension from the processed configuration
     */
    public function __construct(
        private array $enabledTypes
    ) {
    }

    public function isEnabled(string $type): bool
    {
        return in_array($type, $this->enabledTypes, true);
    }

    public function getEnabledTypes(): array
    {
        return $this->enabledTypes;
    }
}
