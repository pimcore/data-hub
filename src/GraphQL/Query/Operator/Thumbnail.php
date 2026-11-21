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
use Pimcore\Model\Asset;
use Pimcore\Model\Element\ElementInterface;

/**
 * @internal
 */
final class Thumbnail extends AbstractOperator
{
    private $thumbnailConfig;

    /**
     * @param array|null $context
     */
    public function __construct(array $config = [], $context = null)
    {
        parent::__construct($config, $context);

        $this->thumbnailConfig = $config['thumbnailConfig'];
    }

    /**
     * @param ElementInterface|null $element
     *
     * @return \stdClass
     */
    public function getLabeledValue($element, ?ResolveInfo $resolveInfo = null)
    {
        $result = new \stdClass();
        $result->label = $this->label;
        if (!$this->thumbnailConfig) {
            return $result;
        }

        $children = $this->getChildren();

        if (!$children) {
            return $result;
        }

        $c = $children[0];

        $valueResolver = $this->getGraphQlService()->buildValueResolverFromAttributes($c);

        $childResult = $valueResolver->getLabeledValue($element, $resolveInfo);
        if ($childResult) {
            $result->value = null;
            if ($childResult->value instanceof Asset\Image || $childResult->value instanceof Asset\Video) {
                $childValue = $result->value = $childResult->value;
                $thumbnail = $childValue->getThumbnail($this->thumbnailConfig, false);
                $result->value = $thumbnail->getPath(['deferredAllowed' => false]);
            }
        }

        return $result;
    }
}
