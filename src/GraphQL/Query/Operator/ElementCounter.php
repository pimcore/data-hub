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

use GraphQL\Type\Definition\ResolveInfo;

class ElementCounter extends AbstractOperator
{
    private $countEmpty;

    public function __construct(array $config, $context = null)
    {
        parent::__construct($config, $context);

        $this->countEmpty = $config['countEmpty'];
    }

    public function getLabeledValue($element, ResolveInfo $resolveInfo = null)
    {
        $result = new \stdClass();
        $result->label = $this->label;

        // Pimcore 5/6 compatibility
        $children = method_exists($this, 'getChildren') ? $this->getChildren() : $this->getChilds();
        $count = 0;

        foreach ($children as $c) {
            $valueResolver = $this->getGraphQlService()->buildValueResolverFromAttributes($c);

            $childResult = $valueResolver->getLabeledValue($element, $resolveInfo);
            $childValues = $childResult->value;

            if ($this->getCountEmpty()) {
                if (is_array($childValues)) {
                    $count += count($childValues);
                } else {
                    $count++;
                }
            } else {
                if (is_array($childValues)) {
                    foreach ($childValues as $childValue) {
                        if ($childValue) {
                            $count++;
                        }
                    }
                } elseif ($childValues) {
                    $count++;
                }
            }
        }

        $result->value = $count;

        return $result;
    }

    /**
     * @return mixed
     */
    public function getCountEmpty()
    {
        return $this->countEmpty;
    }

    /**
     * @param mixed $countEmpty
     */
    public function setCountEmpty($countEmpty)
    {
        $this->countEmpty = $countEmpty;
    }
}
