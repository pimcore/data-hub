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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * @internal
 */
final class GeopointType extends ObjectType
{
    protected static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\Geopoint();
        if (!self::$instance) {
            $config =
                [
                    'fields' => [
                        'longitude' => [
                            'type' => Type::float(),
                            'resolve' => [$resolver, 'resolveLongitude'],
                        ],
                        'latitude' => [
                            'type' => Type::float(),
                            'resolve' => [$resolver, 'resolveLatitude'],
                        ],

                    ],
                ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }
}
