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

use Pimcore\Bundle\DataHubBundle\Service\AdapterAvailabilityServiceInterface;
use Pimcore\Bundle\StudioBackendBundle\Perspective\Model\ContextPermissionData;
use Pimcore\Bundle\StudioBackendBundle\Perspective\Service\ContextPermissionsServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @internal
 */
final readonly class StudioContextPermissionsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ContextPermissionsServiceInterface $permissionsService,
        private AdapterAvailabilityServiceInterface $adapterAvailabilityService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'addContextPermissions',
        ];
    }

    public function addContextPermissions(): void
    {
        // Without the context permission the Data Hub entry is dropped from every perspective,
        // which is what makes a system with no enabled adapter type unreachable in Studio.
        if ($this->adapterAvailabilityService->getEnabledTypes() === []) {
            return;
        }

        $this->permissionsService->add(
            new ContextPermissionData('dataHubConfiguration', 'automationIntegration')
        );
    }
}
