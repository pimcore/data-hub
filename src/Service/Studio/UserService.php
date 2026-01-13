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

use Pimcore\Bundle\DataHubBundle\Event\Studio\PreResponse\PermissionUserEvent;
use Pimcore\Bundle\DataHubBundle\Hydrator\PermissionUserHydratorInterface;
use Pimcore\Model\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/** @internal */
final readonly class UserService implements UserServiceInterface
{
    public function __construct(
        private PermissionUserHydratorInterface $permissionUserHydrator,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function getUsers(string $type): array
    {
        $list = new User\Listing();
        if ($type === 'role') {
            $list = new User\Role\Listing();
        }

        $list->setCondition('type = ? AND id != 1', [$type]);
        $list->setOrder('ASC');
        $list->setOrderKey('name');

        $users = [];
        foreach ($list->getItems() as $user) {
            if ($user->getId() && $user->getName() !== 'system') {
                $hydratedUser = $this->permissionUserHydrator->hydrate($user, $type);

                $this->eventDispatcher->dispatch(
                    new PermissionUserEvent($hydratedUser),
                    PermissionUserEvent::EVENT_NAME
                );

                $users[] = $hydratedUser;
            }
        }

        return $users;
    }
}
