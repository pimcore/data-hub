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

/**
 * @internal
 */
final class GeoboundsType extends ObjectType
{
    protected static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\Geobounds();
        if (!self::$instance) {
            $config =
                [
                    'fields' => [
                        'northEast' => [
                            'type' => GeopointType::getInstance(),
                            'resolve' => [$resolver, 'resolveNorthEast'],
                        ],
                        'southWest' => [
                            'type' => GeopointType::getInstance(),
                            'resolve' => [$resolver, 'resolveSouthWest'],
                        ],

                    ],
                ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }
}
