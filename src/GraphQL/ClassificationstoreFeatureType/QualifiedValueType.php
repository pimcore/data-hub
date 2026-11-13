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
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

/**
 * One qualified value of a key: its language, variant and position and the value typed through the csFeature union.
 *
 * @internal
 */
final class QualifiedValueType extends ObjectType
{
    /** @var \WeakMap<Service, static> one type per service: its fields resolve against the service it is asked for */
    private static \WeakMap $instances;

    /**
     * @return QualifiedValueType
     */
    public static function getInstance(Service $service)
    {
        self::$instances ??= new \WeakMap();
        if (!isset(self::$instances[$service])) {
            self::$instances[$service] = new static([
                'name' => 'csQualifiedValue',
                // lazy, the union contains the csFeatureMultiValue type, which publishes this one
                'fields' => static fn (): array => [
                    'language' => Type::string(),
                    'variant' => Type::string(),
                    'position' => Type::int(),
                    'value' => $service->getClassificationStoreTypeDefinition('cs_feature'),
                ],
            ]);
        }

        return self::$instances[$service];
    }
}
