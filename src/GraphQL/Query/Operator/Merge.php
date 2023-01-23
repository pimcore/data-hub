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
use Pimcore\Model\Element\ElementInterface;

/**
 * @deprecated will be removed in Data Hub 2
 */
class Merge extends AbstractOperator
{
    private $flatten = true;
    private $unique;

    /**
     * @param array $config
     * @param array|null $context
     */
    public function __construct(array $config, $context = null)
    {
        parent::__construct($config, $context);

        $this->unique = $config['unique'];
    }

    /**
     * @param ElementInterface|null $element
     * @param ResolveInfo|null $resolveInfo
     *
     * @return \stdClass
     *
     * @throws \Exception
     */
    public function getLabeledValue($element, ResolveInfo $resolveInfo = null)
    {
        $result = new \stdClass();
        $result->label = $this->label;
        $result->isArrayType = true;

        $children = $this->getChildren();

        $resultItems = [];

        foreach ($children as $c) {
            $valueResolver = $this->getGraphQlService()->buildValueResolverFromAttributes($c);

            $childResult = $valueResolver->getLabeledValue($element, $resolveInfo);
            if ($childResult === null) {
                continue;
            }
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
     * @return bool
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
