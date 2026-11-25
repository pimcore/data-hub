<?php

declare(strict_types=1);

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
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\QuantityValueUnitType;
use Pimcore\Model\DataObject\Data\QuantityValueRange;

/**
 * The bounds and the unit of a quantityValueRange key; each member can be open and resolves to null then.
 *
 * @internal
 */
final class QuantityValueRangeType extends ObjectType
{
    /** @var static|null */
    protected static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static([
                'name' => 'csQuantityValueRange',
                'fields' => [
                    'minimum' => [
                        'type' => Type::float(),
                        'resolve' => static fn (mixed $value = null): int|float|null
                            => $value instanceof QuantityValueRange ? $value->getMinimum() : null,
                    ],
                    'maximum' => [
                        'type' => Type::float(),
                        'resolve' => static fn (mixed $value = null): int|float|null
                            => $value instanceof QuantityValueRange ? $value->getMaximum() : null,
                    ],
                    'unit' => [
                        'type' => QuantityValueUnitType::getInstance(),
                        'resolve' => static fn (mixed $value = null): ?array
                            => $value instanceof QuantityValueRange ? $value->getUnit()?->getObjectVars() : null,
                    ],
                ],
            ]);
        }

        return self::$instance;
    }
}
