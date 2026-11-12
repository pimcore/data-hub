<?php declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType\QualifiedValueReader;
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeInterface\CsFeature;

/**
 * @internal
 */
final class MultiValueType extends ObjectType
{
    /** @var \WeakMap<Service, static> one type per service: its fields resolve against the service it is asked for */
    private static \WeakMap $instances;

    /**
     * @return MultiValueType
     */
    public static function getInstance(Service $service)
    {
        self::$instances ??= new \WeakMap();
        if (!isset(self::$instances[$service])) {
            self::$instances[$service] = new static([
                'name' => 'csFeatureMultiValue',
                'interfaces' => [CsFeature::getInstance()],
                'fields' => static function () use ($service): array {
                    $fields = Helper::getCommonFields();
                    $fields['values'] = [
                        'type' => Type::listOf(QualifiedValueType::getInstance($service)),
                        'resolve' => static function ($value = null) {
                            if ($value instanceof FeatureDescriptor) {
                                return QualifiedValueReader::entries($value);
                            }
                        },
                    ];

                    return $fields;
                },
            ]);
        }

        return self::$instances[$service];
    }
}
