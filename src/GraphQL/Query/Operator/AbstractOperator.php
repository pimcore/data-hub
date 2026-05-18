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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator;

use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

abstract class AbstractOperator implements OperatorInterface
{
    use ServiceTrait;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var array
     */
    protected $context;

    /**
     * @var array
     */
    protected $children;

    /**
     * @param array|null $context
     */
    public function __construct(array $config = [], $context = null)
    {
        $this->label = $config['label'];
        $this->children = $config['children'];
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return bool
     */
    public function expandLocales()
    {
        return false;
    }

    /**
     * @return array|null
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array $context
     *
     * @return void
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return void
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }
}
