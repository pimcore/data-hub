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

use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\Element\ElementInterface;

/**
 * @internal
 */
final class DateFormatter extends AbstractOperator
{
    /**
     * @var string|null
     */
    private $format;

    /**
     * @param array|null $context
     */
    public function __construct(array $config, $context = null)
    {
        parent::__construct($config, $context);

        $this->format = ($config['format'] ? $config['format'] : null);
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
        $result->value = null;

        $children = $this->getChildren();

        if (!$children) {
            return $result;
        }

        $c = $children[0];
        $valueResolver = $this->getGraphQlService()->buildValueResolverFromAttributes($c);

        $childResult = $valueResolver->getLabeledValue($element, $resolveInfo);
        if (!is_null($childResult)) {
            $childResult = $this->format($childResult->value);
            $result->value = $childResult;
        }

        return $result;
    }

    /**
     * @param int|Carbon $theValue
     *
     * @return int|string
     */
    public function format($theValue)
    {
        if ($theValue) {
            if (is_integer($theValue)) {
                $theValue = Carbon::createFromTimestamp($theValue, date_default_timezone_get());
            }
            if ($this->format) {
                if ($theValue instanceof Carbon) {
                    $timestamp = $theValue->getTimestamp();

                    $theValue = date($this->format, $timestamp);
                }
            } else {
                if ($theValue instanceof Carbon) {
                    $theValue = $theValue->toDateString();
                }
            }
        }

        return $theValue;
    }
}
