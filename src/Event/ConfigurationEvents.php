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
