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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class GeopointType extends ObjectType
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
                            'resolve' => [$resolver, 'resolveLongitude']
                        ],
                        'latitude' => [
                            'type' => Type::float(),
                            'resolve' => [$resolver, 'resolveLatitude']
                        ],

                    ],
                ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }
}
