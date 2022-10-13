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

class Concatenator extends AbstractOperator
{
    private $glue;
    private $forceValue;

    public function __construct(array $config, $context = null)
    {
        parent::__construct($config, $context);

        $this->glue = $config['glue'];
        $this->forceValue = $config['forceValue'] ?? false;
    }

    public function getLabeledValue($element, ResolveInfo $resolveInfo = null)
    {
        $result = new \stdClass();
        $result->label = $this->label;

        $hasValue = true;
        if (!$this->forceValue) {
            $hasValue = false;
        }

        $children = $this->getChildren();
        $valueArray = [];

        foreach ($children as $c) {
            $valueResolver = $this->getGraphQlService()->buildValueResolverFromAttributes($c);

            $childResult = $valueResolver->getLabeledValue($element, $resolveInfo);
            if ($childResult == null) {
                continue;
            }
            $childValues = $childResult->value;
            if ($childValues && !is_array($childValues)) {
                $childValues = [$childValues];
            }

            if (is_array($childValues)) {
                foreach ($childValues as $value) {
                    if (!$hasValue) {
                        if (is_object($value) && method_exists($value, 'isEmpty')) {
                            $hasValue = !$value->isEmpty();
                        } else {
                            $hasValue = !empty($value);
                        }
                    }

                    if ($value !== null) {
                        $valueArray[] = $value;
                    }
                }
            }
        }

        if ($hasValue) {
            $result->value = implode($this->glue, $valueArray);

            return $result;
        }

        $result->empty = true;

        return $result;
    }
}
