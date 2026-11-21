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

namespace Pimcore\Bundle\DataHubBundle\Service\Studio;

use Pimcore\Bundle\DataHubBundle\Service\AdapterAvailabilityServiceInterface;
use Pimcore\Bundle\StudioBackendBundle\Setting\Provider\SettingsProviderInterface;

/**
 * Publishes the adapter types this system offers to the Studio frontend, which needs them to decide
 * which adapter types it may offer for creation.
 *
 * @internal
 */
final readonly class DataHubSettingsProvider implements SettingsProviderInterface
{
    public function __construct(
        private AdapterAvailabilityServiceInterface $adapterAvailabilityService
    ) {
    }

    public function getSettings(): array
    {
        return [
            'data_hub_enabled_adapters' => $this->adapterAvailabilityService->getEnabledTypes(),
        ];
    }
}
