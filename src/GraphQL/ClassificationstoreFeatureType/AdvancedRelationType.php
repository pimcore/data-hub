<?php

declare(strict_types=1);

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
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\ElementMetadataKeyValuePairType;

/**
 * An element with the metadata columns of its relation, shared by every key of the type.
 *
 * @internal
 */
final class AdvancedRelationType extends ObjectType
{
    protected static $instance = [];

    /**
     * @return AdvancedRelationType
     */
    public static function getInstance(string $name, UnionType $elementType)
    {
        if (!isset(self::$instance[$name])) {
            $config =
                [
                    'name' => $name,
                    'fields' => [
                        'element' => $elementType,
                        'metadata' => Type::listOf(ElementMetadataKeyValuePairType::getInstance()),
                    ],
                ];
            self::$instance[$name] = new static($config);
        }

        return self::$instance[$name];
    }
}
