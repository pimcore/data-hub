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

namespace Pimcore\Bundle\DataHubBundle\EventListener;

use Pimcore\Event\Admin\IndexActionSettingsEvent;

class AdminListener
{
    /**
     * Handles INDEX_ACTION_SETTINGS event and adds custom admin UI settings
     *
     * @param IndexActionSettingsEvent $event
     */
    public function addIndexSettings(IndexActionSettingsEvent $event)
    {
        $event->addSetting('data-hub-writeable', (new \Pimcore\Bundle\DataHubBundle\Configuration(null, null))->isWriteable());
    }
}
