<?php

/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
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
