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

namespace Pimcore\Bundle\DataHubBundle\Event\Config;

use Pimcore\Bundle\DataHubBundle\Model\SpecialEntitySetting;
use Symfony\Contracts\EventDispatcher\Event;

class SpecialEntitiesEvent extends Event
{
    protected $specialSettings;
    protected $config;

    public function __construct(array $specialSettings, array $config)
    {
        $this->specialSettings = $specialSettings;
        $this->config = $config;
    }

    /**
     * @return SpecialEntitySetting[]
     */
    public function getSpecialSettings(): array
    {
        return $this->specialSettings;
    }

    public function addSpecialSetting(SpecialEntitySetting $setting)
    {
        $this->specialSettings[] = $setting;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
