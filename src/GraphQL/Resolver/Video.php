<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Asset\Image;


class Video
{

    use ServiceTrait;

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return string|null
     */
    public function resolveType($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        if ($value instanceof \Pimcore\Model\DataObject\Data\Video) {
            return $value->getType();
        }

        return null;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveTitle($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        if ($value instanceof \Pimcore\Model\DataObject\Data\Video) {
            return $value->getTitle();
        }

        return null;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return string|null
     */
    public function resolveDescription($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        if ($value instanceof \Pimcore\Model\DataObject\Data\Video) {
            return $value->getDescription();
        }

        return null;
    }


    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return string|null
     */
    public function resolvePoster($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        if ($value instanceof \Pimcore\Model\DataObject\Data\Video) {
            $asset = $value->getPoster();
            if ($asset instanceof Image) {
                $data = new ElementDescriptor();
                $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();
                $fieldHelper->extractData($data, $asset, $args, $context, $resolveInfo);

                $data['data'] = $data['data'] ? base64_encode($data['data']) : null;
                $data['__elementSubtype'] = $asset->getType();
                return $data;
            }
        }

        return null;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return string|null
     */
    public function resolveData($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        if ($value instanceof \Pimcore\Model\DataObject\Data\Video) {
            if ($value->getType() == "asset" && $value->getData() instanceof \Pimcore\Model\Asset\Video) {
                $data = new ElementDescriptor();
                $asset = $value->getData();
                $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();
                $fieldHelper->extractData($data, $asset, $args, $context, $resolveInfo);
                $data['data'] = $data['data'] ? base64_encode($data['data']) : null;
                $data['__elementSubtype'] = $asset->getType();
                return $data;
            } else {

                if ($value->getData()) {
                    $data = ["id" => $value->getData()];
                    return $data;
                }
            }
        }

        return null;
    }

}
