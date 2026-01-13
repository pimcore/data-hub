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

namespace Pimcore\Bundle\DataHubBundle\Hydrator;

use Pimcore\Bundle\DataHubBundle\Schema\PermissionUser;
use Pimcore\Model\User;

/**
 * @internal
 */
final readonly class PermissionUserHydrator implements PermissionUserHydratorInterface
{
    public function hydrate(User|User\Role $user, string $type): PermissionUser
    {
        return new PermissionUser(
            id: $user->getId(),
            text: $user->getName(),
            elementType: $type
        );
    }
}

