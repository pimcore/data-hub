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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;

/**
 * @deprecated will be removed in Data Hub 2
 */
class Merge
{
    use ServiceTrait;

    /** @var string|null */
    protected $typeName;

    /** @var array|null */
    protected $attributes;

    /** @var ClassDefinition|null */
    protected $class;

    /** @var object|null */
    protected $container;

    /**
     * @param string|null $typeName
     * @param array|null $attributes
     * @param ClassDefinition|null $class
     * @param object|null $container
     */
    public function __construct($typeName = null, $attributes = null, $class = null, $container = null)
    {
        $this->typeName = $typeName;
        $this->attributes = $attributes;
        $this->class = $class;
        $this->container = $container;
    }

    /**
     * @param mixed $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array|null
     *
     * @throws \Exception
     */
    public function resolve($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        /** @var \Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator\AbstractOperator $operatorImpl */
        $operatorImpl = $this->getGraphQlService()->buildQueryOperator($this->typeName, $this->attributes);

        $element = AbstractObject::getById($value['id']);
        $valueFromOperator = $operatorImpl->getLabeledValue($element, $resolveInfo);

        return $valueFromOperator?->value;
    }
}
