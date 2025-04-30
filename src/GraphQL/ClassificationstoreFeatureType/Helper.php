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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;

/**
 * @internal
 */
final class Helper extends ObjectType
{
    /**
     * @return array
     */
    public static function getCommonFields()
    {
        $fields = [
            'id' => [
                'type' => Type::int(),
                'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                    if ($value instanceof FeatureDescriptor) {
                        return $value->getId();
                    }
                },
            ],
            'name' => [
                'type' => Type::string(),
                'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                    if ($value instanceof FeatureDescriptor) {
                        $keyConfig = KeyConfig::getById($value->getId());
                        if ($keyConfig) {
                            return $keyConfig->getName();
                        }
                    }
                },
            ],
            'title' => [
                'type' => Type::string(),
                'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                    if ($value instanceof FeatureDescriptor) {
                        $keyConfig = KeyConfig::getById($value->getId());
                        if ($keyConfig) {
                            return $keyConfig->getTitle();
                        }
                    }

                    return null;
                },
            ],
            'description' => [
                'type' => Type::string(),
                'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                    if ($value instanceof FeatureDescriptor) {
                        $keyConfig = KeyConfig::getById($value->getId());
                        if ($keyConfig) {
                            return $keyConfig->getDescription();
                        }
                    }
                },
            ],
            'type' => [
                'type' => Type::string(),
                'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
                    if ($value instanceof FeatureDescriptor) {
                        return $value->getType();
                    }
                },
            ],
        ];

        return $fields;
    }
}
