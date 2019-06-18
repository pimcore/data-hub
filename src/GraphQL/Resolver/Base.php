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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\AbstractObject;


class Base
{

    use ServiceTrait;

    protected $typeName;

    protected $attributes;

    protected $class;

    protected $container;

    /**
     * Base constructor.
     * @param $typeName
     * @param $attributes
     * @param $class
     * @param $container
     */
    public function __construct($typeName, $attributes, $class, $container)
    {
        $this->typeName = $typeName;
        $this->attributes = $attributes;
        $this->class = $class;
        $this->container = $container;
    }


    public function resolve($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        /** @var $operatorImpl \Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator\AbstractOperator */
        $operatorImpl = $this->getGraphQlService()->buildQueryOperator($this->typeName, $this->attributes);

        $element = AbstractObject::getById($value['id']);
        $valueFromOperator = $operatorImpl->getLabeledValue($element, $resolveInfo);
        if ($valueFromOperator) {
            return $valueFromOperator->value;
        } else {
            return null;
        }
    }

    /**
     * Helper method that allows dynamic inspection into the resolver attributes.
     *
     * @param string $type
     * @return string|null
     */
    public function getResolverAttribute(string $type): string
    {
        if (isset($this->attributes['children'][0]) && !empty($this->attributes['children'][0])) {
            return $this->attributes['children'][0]['attributes'][$type];
        } else {
            return null;
        }
    }
}

