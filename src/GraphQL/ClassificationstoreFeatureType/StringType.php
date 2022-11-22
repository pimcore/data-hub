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
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeInterface\CsFeature;

class StringType extends ObjectType
{
    protected static $instance = [];

    /**
     * @param string $name
     * @param string $valueField
     *
     * @return StringType
     */
    public static function getInstance(string $name, string $valueField)
    {
        if (!isset(self::$instance[$name])) {
            $fields = Helper::getCommonFields();
            $fields[$valueField] = [
                'type' => Type::string(),
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
