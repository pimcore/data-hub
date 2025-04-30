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

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\Element\ElementInterface;

/**
 * @internal
 */
final class Concatenator extends AbstractOperator
{
    private $glue;

    private $forceValue;

    /**
     * @param array|null $context
     */
    public function __construct(array $config, $context = null)
    {
        parent::__construct($config, $context);

        $this->glue = $config['glue'];
        $this->forceValue = $config['forceValue'] ?? false;
    }

    /**
     * @param ElementInterface|null $element
     *
     * @return \stdClass
     *
     * @throws \Exception
     */
    public function getLabeledValue($element, ?ResolveInfo $resolveInfo = null)
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
            if (!$childResult = $valueResolver->getLabeledValue($element, $resolveInfo)) {
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
