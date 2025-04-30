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

namespace Pimcore\Bundle\DataHubBundle\Event;

final class AdminEvents
{
    /**
     * Allows you to modify/append the configuration list.
     *
     * Arguments:
     *  - list | the configuration list
     *
     * @Event("Pimcore\Event\Model\GenericEvent")
     *
     * @var string
     */
    const CONFIGURATION_LIST = 'pimcore.datahub.admin.configuration.list';
}
