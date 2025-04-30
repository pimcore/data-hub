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

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document;
use Pimcore\Model\Element\ElementInterface;

final class ElementDescriptor extends BaseDescriptor
{
    public function __construct(?ElementInterface $element = null)
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
