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

namespace Pimcore\Bundle\DataHubBundle\Event;

final class ConfigurationEvents
{
    /**
     * Fired after a configuration was deleted
     *
     * Arguments:
     *  - configuration | the original configuration instance
     *
     * @Event("Pimcore\Event\Model\GenericEvent")
     *
     * @var string
     */
    const CONFIGURATION_POST_DELETE = 'pimcore.datahub.configuration.postDelete';

    /**
     * Fired before a configuration gets saved
     *
     * Arguments:
     *  - configuration | the original configuration instance
     *
     * @Event("Pimcore\Event\Model\GenericEvent")
     *
     * @var string
     */
    const CONFIGURATION_PRE_SAVE = 'pimcore.datahub.configuration.preSave';

    /**
     * Fired after a configuration was saved
     *
     * Arguments:
     *  - configuration | the original configuration instance
     *
     * @Event("Pimcore\Event\Model\GenericEvent")
     *
     * @var string
     */
    const CONFIGURATION_POST_SAVE = 'pimcore.datahub.configuration.postSave';
}
