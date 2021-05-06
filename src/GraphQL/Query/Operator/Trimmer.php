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

class Trimmer extends AbstractOperator
{
    const LEFT = 1;
    const RIGHT = 2;
    const BOTH = 3;

    private $trim;

    public function __construct(array $config, $context = null)
    {
        parent::__construct($config, $context);

        $this->trim = $config['trim'];
    }

    public function getLabeledValue($element, ResolveInfo $resolveInfo = null)
    {
        $result = new \stdClass();
        $result->label = $this->label;

        // Pimcore 5/6 compatibility
        $children = method_exists($this, 'getChildren') ? $this->getChildren() : $this->getChilds();

        if (!$children) {
            return $result;
        } else {
            $c = $children[0];

            $valueArray = [];

            $valueResolver = $this->getGraphQlService()->buildValueResolverFromAttributes($c);

            $childResult = $valueResolver->getLabeledValue($element, $resolveInfo);
            $isArrayType = $childResult->isArrayType;
            $childValues = $childResult->value;
            if ($childValues && !$isArrayType) {
                $childValues = [$childValues];
            }

            if ($childValues) {
                /** @var string $childValue */
                foreach ($childValues as $childValue) {
                    if ($this->trim == self::LEFT) {
                        $childValue = ltrim($childValue);
                    } elseif ($this->trim == self::RIGHT) {
                        $childValue = rtrim($childValue);
                    } elseif ($this->trim == self::BOTH) {
                        $childValue = trim($childValue);
                    }
                    $valueArray[] = $childValue;
                }
            }

            $result->isArrayType = $isArrayType;
            if ($isArrayType) {
                $result->value = $valueArray;
            } else {
                $result->value = $valueArray[0];
            }
        }

        return $result;
    }
}
