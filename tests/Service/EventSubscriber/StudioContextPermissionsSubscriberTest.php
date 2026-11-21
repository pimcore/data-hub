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

namespace Pimcore\Bundle\DataHubBundle\EventSubscriber;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\Service\AdapterAvailabilityServiceInterface;
use Pimcore\Bundle\StudioBackendBundle\Perspective\Service\ContextPermissionsServiceInterface;

class StudioContextPermissionsSubscriberTest extends Unit
{
    public function testTheDataHubContextPermissionIsRegisteredForAnEnabledAdapter(): void
    {
        $permissionsService = $this->createMock(ContextPermissionsServiceInterface::class);
        $permissionsService->expects($this->once())->method('add');

        $this->createSubscriber($permissionsService, ['graphql'])->addContextPermissions();
    }

    public function testTheDataHubContextPermissionIsWithheldWhenNoAdapterIsEnabled(): void
    {
        $permissionsService = $this->createMock(ContextPermissionsServiceInterface::class);
        $permissionsService->expects($this->never())->method('add');

        $this->createSubscriber($permissionsService, [])->addContextPermissions();
    }

    private function createSubscriber(
        ContextPermissionsServiceInterface $permissionsService,
        array $enabledTypes
    ): StudioContextPermissionsSubscriber {
        $adapterAvailabilityService = $this->createMock(AdapterAvailabilityServiceInterface::class);
        $adapterAvailabilityService->method('getEnabledTypes')->willReturn($enabledTypes);

        return new StudioContextPermissionsSubscriber($permissionsService, $adapterAvailabilityService);
    }
}
