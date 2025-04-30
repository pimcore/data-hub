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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\TypeInterface;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

/**
 * @internal
 */
final class Property
{
    public static $instance;

    /**
     * @return InterfaceType
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance =
                new InterfaceType(
                    [
                        'name' => 'property',
                        'fields' => [
                            'name' => [
                                'type' => Type::string(),        // name of property
                            ],
                            'type' => [
                                'type' => Type::string(),        // property type
                            ],
                        ],
                    ]

                );
        }

        return self::$instance;
    }
}
