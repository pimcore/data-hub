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

class Trimmer extends AbstractOperator
{
    const LEFT = 1;
    const RIGHT = 2;
    const BOTH = 3;

    private $trim;

    /**
     * @param array $config
     * @param array|null $context
     */
    public function __construct(array $config, $context = null)
    {
        parent::__construct($config, $context);

        $this->trim = $config['trim'];
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
