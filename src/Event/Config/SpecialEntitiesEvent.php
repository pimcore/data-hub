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

namespace Pimcore\Bundle\DataHubBundle\Event\Config;

use Pimcore\Bundle\DataHubBundle\Model\SpecialEntitySetting;
use Symfony\Contracts\EventDispatcher\Event;

final class SpecialEntitiesEvent extends Event
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
