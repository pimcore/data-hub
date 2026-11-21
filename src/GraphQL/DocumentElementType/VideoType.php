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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\AssetType\AssetType;
use Pimcore\Bundle\DataHubBundle\GraphQL\RelationHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\Document\Editable\Video;

/**
 * @internal
 */
final class VideoType extends ObjectType
{
    protected static $instance;

    /**
     *
     * @return static
     */
    public static function getInstance(Service $graphQlService, AssetType $assetType)
    {
        if (!self::$instance) {
            $config =
                [
                    'name' => 'document_editableVideo',
                    'fields' => [
                        '_editableType' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                                if ($value) {
                                    return $value->getType();
                                }
                            },
                        ],
                        '_editableName' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                                if ($value) {
                                    return $value->getName();
                                }
                            },
                        ],
                        'id' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                                if ($value instanceof Video) {
                                    return $value->getId();
                                }
                            },
                        ],
                        'type' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                                if ($value) {
                                    return $value->getVideoType();
                                }
                            },
                        ],
                        'title' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                                if ($value instanceof Video) {
                                    return $value->getTitle();
                                }
                            },
                        ],
                        'description' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                                if ($value instanceof Video) {
                                    return $value->getDescription();
                                }
                            },
                        ],
                        'posterAsset' => [
                            'type' => $assetType,
                            'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) use ($graphQlService) {
                                if ($value instanceof Video) {
                                    $relation = $value->getPosterAsset();
                                    if ($relation) {
                                        $data = RelationHelper::processRelation($relation, $graphQlService, $args, $context, $resolveInfo);

                                        return $data;
                                    }
                                }

                                return null;
                            },
                        ],
                        'videoAsset' => [
                            'type' => $assetType,
                            'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) use ($graphQlService) {
                                if ($value instanceof Video) {
                                    $relation = $value->getVideoAsset();
                                    if ($relation) {
                                        $data = RelationHelper::processRelation($relation, $graphQlService, $args, $context, $resolveInfo);

                                        return $data;
                                    }
                                }

                                return null;
                            },
                        ],
                    ],
                ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }
}
