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

namespace Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model;

use Pimcore\Model\DataObject\OwnerAwareFieldInterface;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Contracts\EventDispatcher\Event;

class PermissionEvent extends Event
{
    /**
     * @var ElementInterface|OwnerAwareFieldInterface $element
     */
    protected $element;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $isGranted = true;

    /**
     * @return OwnerAwareFieldInterface|ElementInterface
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param OwnerAwareFieldInterface|ElementInterface $element
     */
    public function setElement($element): void
    {
        $this->element = $element;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function isGranted(): bool
    {
        return $this->isGranted;
    }

    public function setIsGranted(bool $isGranted): void
    {
        $this->isGranted = $isGranted;
    }

    /**
     * @param ElementInterface|OwnerAwareFieldInterface $element
     * @param string $type
     */
    public function __construct($element, $type)
    {
        $this->element = $element;
        $this->type = $type;
    }
}
