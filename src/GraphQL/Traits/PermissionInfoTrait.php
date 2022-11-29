<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Traits;

trait PermissionInfoTrait
{
    /**
     * @var bool
     */
    protected $omitPermissionCheck = false;

    /**
     * @return bool
     */
    public function getOmitPermissionCheck()
    {
        return $this->omitPermissionCheck;
    }

    public function setOmitPermissionCheck(bool $omitPermissionCheck)
    {
        $this->omitPermissionCheck = $omitPermissionCheck;
    }
}
