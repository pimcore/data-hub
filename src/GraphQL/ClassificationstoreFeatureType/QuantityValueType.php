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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeInterface\CsFeature;

class QuantityValueType extends ObjectType
{
    protected static $instance = [];

    /**
     * @param Service $service
     * @param string $name
     * @param string $innerType
     * @param string $fieldname
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
                'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                    if ($value instanceof FeatureDescriptor) {
                        return $value->getValue();
                    }
                }
            ];

            $config =
                [
                    'name' => $name,
                    'interfaces' => [CsFeature::getInstance()],
                    'fields' => $fields
                ];
            self::$instance[$name] = new static($config);
        }

        return self::$instance[$name];
    }
}
