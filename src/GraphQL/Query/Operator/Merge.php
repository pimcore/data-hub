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

class Merge extends AbstractOperator
{
    private $flatten = true;
    private $unique;

    public function __construct(array $config, $context = null)
    {
        parent::__construct($config, $context);

        $this->unique = $config['unique'];
    }

    public function getLabeledValue($element, ResolveInfo $resolveInfo = null)
    {
        $result = new \stdClass();
        $result->label = $this->label;
        $result->isArrayType = true;

        // Pimcore 5/6 compatibility
        $children = method_exists($this, 'getChildren') ? $this->getChildren() : $this->getChilds();

        $resultItems = [];

        foreach ($children as $c) {
            $valueResolver = $this->getGraphQlService()->buildValueResolverFromAttributes($c);

            $childResult = $valueResolver->getLabeledValue($element, $resolveInfo);
            $childValues = $childResult->value;

            if ($this->flatten) {
                if (is_array($childValues)) {
                    foreach ($childValues as $childValue) {
                        if ($childValue) {
                            $resultItems[] = $childValue;
                        }
                    }
                } elseif ($childValues) {
                    $resultItems[] = $childValues;
                }
            } else {
                if ($childValues) {
                    $resultItems[] = $childValues;
                }
            }
        }

        if ($this->getUnique()) {
            $resultItems = array_unique($resultItems);
        }
        $result->value = $resultItems;

        return $result;
    }

    /**
     * @return mixed
     */
    public function getFlatten()
    {
        return $this->flatten;
    }

    /**
     * @param mixed $flatten
     */
    public function setFlatten($flatten)
    {
        $this->flatten = $flatten;
    }

    /**
     * @return mixed
     */
    public function getUnique()
    {
        return $this->unique;
    }

    /**
     * @param mixed $unique
     */
    public function setUnique($unique)
    {
        $this->unique = $unique;
    }
}
