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

use Carbon\Carbon;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeInterface\CsFeature;

class DateType extends ObjectType
{
    /** @var static|null */
    protected static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $fields = Helper::getCommonFields();
            $fields['date'] = [
                'type' => Type::string(),
                'args' => [
                    ['name' => 'format',
                        'type' => Type::string(),
                        'description' => 'see Carbon::format'
                    ]
                ],
                'resolve' => static function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) {
                    if ($value instanceof FeatureDescriptor) {
                        $dateValue = $value->getValue();
                        if ($dateValue instanceof Carbon) {
                            if ($args['format']) {
                                $format = $args['format'];
                                $formattedValue = $dateValue->format($format);
                            } else {
                                $formattedValue = (string)$dateValue;
                            }

                            return $formattedValue;
                        }
                    }
                }
            ];

            $config =
                [
                    'name' => 'csFeatureDate',
                    'interfaces' => [CsFeature::getInstance()],
                    'fields' => $fields

                ];
            self::$instance = new static($config);
        }

        return self::$instance;
    }
}
