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

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\Service\AdapterAvailabilityServiceInterface;

class DataHubSettingsProviderTest extends Unit
{
    public function testTheEnabledAdapterTypesArePublishedToTheFrontend(): void
    {
        $this->assertSame(
            ['data_hub_enabled_adapters' => ['graphql', 'simpleRest']],
            $this->createProvider(['graphql', 'simpleRest'])->getSettings()
        );
    }

    public function testADisabledAdapterTypeIsNotPublished(): void
    {
        $this->assertSame(
            ['data_hub_enabled_adapters' => ['simpleRest']],
            $this->createProvider(['simpleRest'])->getSettings()
        );
    }

    private function createProvider(array $enabledTypes): DataHubSettingsProvider
    {
        $adapterAvailabilityService = $this->createMock(AdapterAvailabilityServiceInterface::class);
        $adapterAvailabilityService->method('getEnabledTypes')->willReturn($enabledTypes);

        return new DataHubSettingsProvider($adapterAvailabilityService);
    }
}
