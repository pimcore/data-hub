<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class SimpleTextType extends ObjectType
{
    protected static $instance;

    public static function getStandardConfig($name)
    {
        return [
            'name' => $name,
            'fields' => [
                'type' => [
                    'type' => Type::string(),
                    'resolve' => static function ($value = null, $args = [], $context, ResolveInfo $resolveInfo = null) {
                        if ($value) {
                            return $value->getType();
                        }
                    }
                ],
                'name' => [
                    'type' => Type::string(),
                    'resolve' => static function ($value = null, $args = [], $context, ResolveInfo $resolveInfo = null) {
                        if ($value) {
                            return $value->getName();
                        }
                    }
                ],
                'text' => [
                    'type' => Type::string(),
                    'resolve' => static function ($value = null, $args = [], $context, ResolveInfo $resolveInfo = null) {
                        if ($value) {
                            return $value->getData();
                        }
                    }
                ],
            ],
        ];
    }
}
