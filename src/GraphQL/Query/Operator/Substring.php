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

class Substring extends AbstractOperator
{
    private $start;

    private $length;

    private $ellipses;

    public function __construct(array $config = [], $context = null)
    {
        parent::__construct($config, $context);

        $this->start = $config['start'];
        $this->length = $config['length'];
        $this->ellipses = $config['ellipses'];
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

            /** @var $childValue string */
            if (is_array($childValues)) {
                foreach ($childValues as $childValue) {
                    $showEllipses = false;
                    if ($childValue && $this->getEllipses()) {
                        $start = $this->getStart() ? $this->getStart() : 0;
                        $length = $this->getLength() ? $this->getLength() : 0;
                        if (strlen($childValue) > $start + $length) {
                            $showEllipses = true;
                        }
                    }

                    $childValue = substr($childValue, $this->getStart(), $this->getLength());
                    if ($showEllipses) {
                        $childValue .= '...';
                    }

                    $valueArray[] = $childValue;
                }
            } else {
                $valueArray[] = $childResult->value;
            }

            $result->isArrayType = $isArrayType;
            if ($isArrayType) {
                $result->value = $valueArray;
            } else {
                $result->value = $valueArray[0];
            }
            $result->$valueArray;
        }

        return $result;
    }

    /**
     * @return int|null
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param int|null $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return int|null
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param int|null $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @return mixed
     */
    public function getEllipses()
    {
        return $this->ellipses;
    }

    /**
     * @param mixed $ellipses
     */
    public function setEllipses($ellipses)
    {
        $this->ellipses = $ellipses;
    }
}
