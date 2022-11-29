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

class GeoboundsType extends ObjectType
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
                            'resolve' => [$resolver, 'resolveNorthEast']
                        ],
                        'southWest' => [
                            'type' => GeopointType::getInstance(),
                            'resolve' => [$resolver, 'resolveSouthWest']
                        ],

                    ],
                ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }
}
