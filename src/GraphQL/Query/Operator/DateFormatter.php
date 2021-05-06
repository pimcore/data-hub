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

use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;

class DateFormatter extends AbstractOperator
{
    /**
     * @var string|null
     */
    private $format;

    public function __construct(array $config, $context = null)
    {
        parent::__construct($config, $context);

        $this->format = ($config['format'] ? $config['format'] : null);
    }

    public function getLabeledValue($element, ResolveInfo $resolveInfo = null)
    {
        $result = new \stdClass();
        $result->label = $this->label;
        $result->value = null;

        // Pimcore 5/6 compatibility
        $children = method_exists($this, 'getChildren') ? $this->getChildren() : $this->getChilds();

        if (!$children) {
            return $result;
        } else {
            $c = $children[0];
            $valueResolver = $this->getGraphQlService()->buildValueResolverFromAttributes($c);

            $childResult = $valueResolver->getLabeledValue($element, $resolveInfo);
            if (!is_null($childResult)) {
                $childResult = $this->format($childResult->value);
                $result->value = $childResult;
            }
        }

        return $result;
    }

    public function format($theValue)
    {
        if ($theValue) {
            if (is_integer($theValue)) {
                $theValue = Carbon::createFromTimestamp($theValue);
            }
            if ($this->format) {
                $timestamp = null;

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
