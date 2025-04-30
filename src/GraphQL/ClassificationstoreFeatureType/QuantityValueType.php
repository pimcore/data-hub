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
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeInterface\CsFeature;

/**
 * @internal
 */
final class QuantityValueType extends ObjectType
{
    protected static $instance = [];

    /**
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public static function getInstance(Service $service, string $name, string $innerType, string $fieldname)
    {
        if (!isset(self::$instance[$name])) {
            $innerType = $service->getDataObjectTypeDefinition($innerType);

            $fields = Helper::getCommonFields();
            $fields[$fieldname] = [
                'type' => $innerType,
                'resolve' => static function ($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null) {
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
