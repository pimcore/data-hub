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

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document;
use Pimcore\Model\Element\ElementInterface;

class ElementDescriptor extends BaseDescriptor
{
    /**
     * @param ElementInterface|null $element
     */
    public function __construct(ElementInterface $element = null)
    {
        parent::__construct();
        if ($element) {
            $this->offsetSet('id', $element->getId());
            $this->offsetSet('__elementType', \Pimcore\Model\Element\Service::getElementType($element));
            $this->offsetSet('__elementSubtype', $element instanceof Concrete ? $element->getClass()->getName() : $element->getType());

            if ($element instanceof Concrete) {
                $subtype = $element->getClass()->getName();

                $this->offsetSet('__elementType', 'object');
                $this->offsetSet('__elementSubtype', $subtype);
            } elseif ($element instanceof Asset) {
                $this->offsetSet('__elementType', 'asset');
                $this->offsetSet('__elementSubtype', $element->getType());
            } elseif ($element instanceof Document) {
                $this->offsetSet('__elementType', 'document');
                $this->offsetSet('__elementSubtype', $element->getType());
            }
        }
    }
}
