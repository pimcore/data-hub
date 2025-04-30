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

namespace Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model;

use Pimcore\Model\DataObject\OwnerAwareFieldInterface;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class PermissionEvent extends Event
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
