<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
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
