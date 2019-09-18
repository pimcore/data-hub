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

class NumericType extends ObjectType
{
    protected static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            $config =
                [
                    'name' => "document_tagNumeric",
                    'fields' => [
                        'type' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context, ResolveInfo $resolveInfo = null) {
                                if ($value instanceof \Pimcore\Model\Document\Tag\Numeric) {
                                    return $value->getType();
                                }
                            }
                        ],
                        'number' => [
                            'type' => Type::string(),
                            'resolve' => static function ($value = null, $args = [], $context, ResolveInfo $resolveInfo = null) {
                                if ($value instanceof \Pimcore\Model\Document\Tag\Numeric) {
                                    return $value->getData();
                                }
                            }
                        ]
                    ],
                ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }
}
