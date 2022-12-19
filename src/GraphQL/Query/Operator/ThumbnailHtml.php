<?php
declare(strict_types=1);

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

/**
 * Class ThumbnailHtml
 *
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator
 */
class ThumbnailHtml extends AbstractOperator
{
    /**
     * @var string|null
     */
    private $thumbnailHtmlConfig;

    /**
     * @param array $config
     * @param array|null $context
     */
    public function __construct(array $config = [], $context = null)
    {
        parent::__construct($config, $context);

        $this->thumbnailHtmlConfig = $config['thumbnailHtmlConfig'];
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
        $result->value = null;

        $children = $this->getChildren();

        if ($children && $this->thumbnailHtmlConfig) {
            $c = $children[0];
            $valueResolver = $this->getGraphQlService()->buildValueResolverFromAttributes($c);
            $childResult = $valueResolver->getLabeledValue($element, $resolveInfo);

            if ($childResult) {
                // We may get a single asset (e.g. regular asset element) or an array of assets (e.g. from a gallery element)
                if ($childResult->value instanceof Asset\Image || $childResult->value instanceof Asset\Video) {
                    $thumbnail = $childResult->value->getThumbnail($this->thumbnailHtmlConfig, false);
                    $result->value = $thumbnail->getHtml();
                } elseif (!empty($childResult->value)) {
                    $result->value = [];
                    foreach ($childResult->value as $value) {
                        $result->value[] = $value['img']->getThumbnail($this->thumbnailHtmlConfig, false)->getHtml();
                    }
                }
            }
        }

        return $result;
    }
}
