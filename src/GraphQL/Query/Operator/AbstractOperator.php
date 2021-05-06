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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator;

use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\DataObject\GridColumnConfig\ConfigElementInterface;

abstract class AbstractOperator implements OperatorInterface
{
    use ServiceTrait;
    /**
     * @var string
     */
    protected $label;

    /**
     * @var mixed
     */
    protected $context;

    /**
     * @var ConfigElementInterface[]
     */
    protected $children;

    public function __construct(array $config = [], $context = null)
    {
        $this->label = $config['label'];
        $this->children = $config['childs'];
        $this->context = $context;
    }

    /**
     * @return ConfigElementInterface[]
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

    public function getContext()
    {
        return $this->context;
    }

    public function setContext($context)
    {
        $this->context = $context;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }
}
