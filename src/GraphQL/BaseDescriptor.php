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

abstract class BaseDescriptor extends \ArrayObject
{
    /**
     *
     * ElementDescriptor constructor - an ElementDescriptor describes something that implements
     * the Pimcore\Model\Element\ElementInterface
     */
    public function __construct()
    {
        parent::__construct([], self::STD_PROP_LIST | self::ARRAY_AS_PROPS);
    }
}
