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
use Pimcore\Model\Asset;
use Pimcore\Model\Element\ElementInterface;

class Thumbnail extends AbstractOperator
{
    private $thumbnailConfig;

    /**
     * @param array $config
     * @param array|null $context
     */
    public function __construct(array $config = [], $context = null)
    {
        parent::__construct($config, $context);

        $this->thumbnailConfig = $config['thumbnailConfig'];
    }

    /**
     * @param ElementInterface|null $element
     * @param ResolveInfo|null $resolveInfo
     *
     * @return \stdClass|null
     */
    public function getLabeledValue($element, ResolveInfo $resolveInfo = null)
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
