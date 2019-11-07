<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\ElementInterface;

class ElementDescriptor extends BaseDescriptor
{

    /**
     * ElementDescriptor constructor.
     * @param ElementInterface|null $element
     */
    public function __construct(ElementInterface $element = null)
    {
        parent::__construct();
        if ($element) {
            $this->id = $element->getId();
            $this->__elementType = \Pimcore\Model\Element\Service::getElementType($element);
            $this->__elementSubtype = $element instanceof Concrete ? $element->getClass()->getName() : $element->getType();


            if ($element instanceof Concrete) {
                $subtype = $element->getClass()->getName();

                $this->__elementType = "object";
                $this->__elementSubtype = $subtype;
            } elseif ($element instanceof Asset) {
                $this->__elementType = "asset";
                $this->__elementSubtype = $element->getType();
            } else if ($element instanceof Document) {
                $this->__elementType = "document";
                $this->__elementSubtype = $element->getType();
            }
        }
    }
}
