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

namespace Pimcore\Bundle\DataHubBundle\Event\Studio\PreResponse;

use Pimcore\Bundle\DataHubBundle\Schema\PermissionUser;
use Pimcore\Bundle\StudioBackendBundle\Event\AbstractPreResponseEvent;

final class PermissionUserEvent extends AbstractPreResponseEvent
{
    public const string EVENT_NAME = 'pre_response.data_hub.permission_user';

    public function __construct(
        private readonly PermissionUser $permissionUser
    ) {
        parent::__construct($permissionUser);
    }

    /**
     * Use this to get additional infos out of the response object
     */
    public function getPermissionUser(): PermissionUser
    {
        return $this->permissionUser;
    }
}
