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
use Pimcore\Model\DataObject\Data\NumericRange;

/**
 * The bounds of a numericRange key; either bound can be open and resolves to null then.
 *
 * @internal
 */
final class NumericRangeType extends ObjectType
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
                'name' => 'csNumericRange',
                'fields' => [
                    'minimum' => [
                        'type' => Type::float(),
                        'resolve' => static fn (mixed $value = null): int|float|null => $value instanceof NumericRange
                            ? $value->getMinimum()
                            : null,
                    ],
                    'maximum' => [
                        'type' => Type::float(),
                        'resolve' => static fn (mixed $value = null): int|float|null => $value instanceof NumericRange
                            ? $value->getMaximum()
                            : null,
                    ],
                ],
            ]);
        }

        return self::$instance;
    }
}
