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

namespace Pimcore\Bundle\DataHubBundle\EventListener;

use Pimcore\Bundle\AdminBundle\Event\IndexActionSettingsEvent;

/**
 * @internal
 */
final class AdminListener
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Handles INDEX_ACTION_SETTINGS event and adds custom admin UI settings
     *
     */
    public function addIndexSettings(IndexActionSettingsEvent $event)
    {
        $event->addSetting('data-hub-writeable', (new \Pimcore\Bundle\DataHubBundle\Configuration(null, null))->isWriteable());
        $this->addEventSetting('allow_introspection', $event);
        $this->addEventSetting('allow_sqlObjectCondition', $event);
    }

    private function addEventSetting(
        string $key,
        IndexActionSettingsEvent $event
    ): void {
        $value = $this->config['graphql'][$key] ?? true;
        $event->addSetting($key, $value);
    }
}
