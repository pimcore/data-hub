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

use Pimcore\Event\Model\ElementEventInterface;
use Symfony\Contracts\EventDispatcher\Event;

class IsValidDataObjectTriggerEvent extends Event
{
    protected bool $isValid;

    protected ElementEventInterface $event;

    public function getEvent(): ElementEventInterface
    {
        return $this->event;
    }

    public function setEvent(ElementEventInterface $event)
    {
        $this->event = $event;

        return $this;
    }

    public function setIsValid(bool $value)
    {
        $this->isValid = $value;
    }

    public function getIsValid(): bool
    {
        return $this->isValid;
    }
}
