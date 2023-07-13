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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\AssetType\AssetType;
use Pimcore\Bundle\DataHubBundle\GraphQL\RelationHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\Document\Editable\Video;
use Pimcore\Model\Element\ElementInterface;

class VideoType extends ObjectType
{
    protected static $instance;

    /**
     * @param Service $graphQlService
     * @param AssetType $assetType
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
                            'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                                if ($value) {
                                    return $value->getType();
                                }
                            }
                        ],
                        '_editableName' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                                if ($value) {
                                    return $value->getName();
                                }
                            }
                        ],
                        'id' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                                if ($value instanceof Video) {
                                    return $value->getId();
                                }
                            }
                        ],
                        'type' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                                if ($value) {
                                    return $value->getVideoType();
                                }
                            }
                        ],
                        'title' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                                if ($value instanceof Video) {
                                    return $value->getTitle();
                                }
                            }
                        ],
                        'description' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                                if ($value instanceof Video) {
                                    return $value->getDescription();
                                }
                            }
                        ],
                        'posterAsset' => [
                            'type' => $assetType,
                            'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) use ($graphQlService) {
                                if ($value instanceof Video) {
                                    /** @var ElementInterface|null $relation */
                                    $relation = $value->getPosterAsset();
                                    if ($relation) {
                                        $data = RelationHelper::processRelation($relation, $graphQlService, $args, $context, $resolveInfo);

                                        return $data;
                                    }
                                }

                                return null;
                            }
                        ],
                        'videoAsset' => [
                            'type' => $assetType,
                            'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) use ($graphQlService) {
                                if ($value instanceof Video) {
                                    $relation = $value->getVideoAsset();
                                    if ($relation) {
                                        $data = RelationHelper::processRelation($relation, $graphQlService, $args, $context, $resolveInfo);

                                        return $data;
                                    }
                                }

                                return null;
                            }
                        ],
                    ],
                ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }
}
