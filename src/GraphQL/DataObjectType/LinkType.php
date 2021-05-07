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

class LinkType extends ObjectType
{
    protected static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\Link();
            $config =
                [
                    'fields' => [
                        'text' => [
                            'type' => Type::string(),
                            'resolve' => [$resolver, 'resolveText']
                        ],
                        'path' => [
                            'type' => Type::string(),
                            'resolve' => [$resolver, 'resolvePath']
                        ]
                    ],
                ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }
}
