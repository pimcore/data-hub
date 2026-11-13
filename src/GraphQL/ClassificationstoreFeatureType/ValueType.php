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
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeInterface\CsFeature;

/**
 * @internal
 */
final class ValueType extends ObjectType
{
    protected static $instance = [];

    /**
     * @param callable|null $resolver receives the same arguments as a field resolver,
     *                                the value is the FeatureDescriptor
     *
     * @return ValueType
     */
    public static function getInstance(string $name, string $valueField, Type $valueType, ?callable $resolver = null)
    {
        if (!isset(self::$instance[$name])) {
            $fields = Helper::getCommonFields();
            $fields[$valueField] = [
                'type' => $valueType,
                'resolve' => $resolver ?? static function ($value = null) {
                    if ($value instanceof FeatureDescriptor) {
                        return $value->getValue();
                    }
                },
            ];

            $config =
                [
                    'name' => $name,
                    'interfaces' => [CsFeature::getInstance()],
                    'fields' => $fields,
                ];
            self::$instance[$name] = new static($config);
        }

        return self::$instance[$name];
    }
}
