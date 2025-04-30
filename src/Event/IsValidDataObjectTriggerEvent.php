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

use Pimcore\Event\Model\ElementEventInterface;
use Symfony\Contracts\EventDispatcher\Event;

class IsValidDataObjectTriggerEvent extends Event
{
    protected bool $isValid;

    protected ElementEventInterface $triggerEvent;

    public function getTriggerEvent(): ElementEventInterface
    {
        return $this->triggerEvent;
    }

    public function setTriggerEvent(ElementEventInterface $triggerEvent)
    {
        $this->triggerEvent = $triggerEvent;

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
