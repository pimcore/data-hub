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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class ScheduledblockDataType extends ObjectType
{
    use ServiceTrait;

    public function __construct(Service $graphQlService)
    {
        $this->graphQlService = $graphQlService;

        $config =
            [
                'name' => 'document_editableScheduledblock_data',
                'fields' => [
                    '_editableType' => [
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value) {
                                return $value->getType();
                            }
                        }
                    ],
                    '_editableName' => [
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if ($value) {
                                return $value->getName();
                            }
                        }
                    ],
                    'key' => [
                        'type' => Type::string(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if (is_array($value)) {
                                return $value['key'];
                            }

                            return null;
                        }
                    ],
                    'date' => [
                        'type' => Type::int(),
                        'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                            if (is_array($value)) {
                                return $value['date'];
                            }

                            return null;
                        }
                    ],
                ],
            ];
        parent::__construct($config);
    }
}
