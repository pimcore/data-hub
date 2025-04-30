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
final class Trimmer extends AbstractOperator
{
    const LEFT = 1;

    const RIGHT = 2;

    const BOTH = 3;

    private $trim;

    /**
     * @param array|null $context
     */
    public function __construct(array $config, $context = null)
    {
        parent::__construct($config, $context);

        $this->trim = $config['trim'];
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
        $children = $this->getChildren();

        if (!$children) {
            return $result;
        }

        $c = $children[0];

        $valueResolver = $this->getGraphQlService()->buildValueResolverFromAttributes($c);

        $childResult = $valueResolver->getLabeledValue($element, $resolveInfo);
        if (!$childResult) {
            return $result;
        }

        if ($childValue = $childResult->value) {
            /** @var string $childValue */
            switch ($this->trim) {
                case self::LEFT:
                    $childValue = ltrim($childValue);

                    break;
                case self::RIGHT:
                    $childValue = rtrim($childValue);

                    break;
                case self::BOTH:
                    $childValue = trim($childValue);

                    break;
            }
        }
        $result->value = $childValue;

        return $result;
    }
}
