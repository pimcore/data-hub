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

namespace Pimcore\Bundle\DataHubBundle\Event\GraphQL;

final class PermissionEvents
{
    /**
     * @Event("Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\PermissionEvent")
     *
     * @var string
     */
    const PRE_CHECK = 'pimcore.datahub.graphql.permission.preCheck';
}
