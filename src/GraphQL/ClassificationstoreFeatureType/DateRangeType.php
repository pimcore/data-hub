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

use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * The bounds of a dateRange key, formatted the way csFeatureDate formats a single date.
 *
 * @internal
 */
final class DateRangeType extends ObjectType
{
    private const FORMAT = 'format';

    /** @var static|null */
    protected static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $formatArg = [
                'name' => self::FORMAT,
                'type' => Type::string(),
                'description' => 'see Carbon::format, e.g. "U" for the seconds since the epoch',
            ];

            self::$instance = new static([
                'name' => 'csDateRange',
                'fields' => [
                    'startDate' => [
                        'type' => Type::string(),
                        'args' => [$formatArg],
                        'resolve' => static fn (mixed $value = null, array $args = []): ?string => self::format(
                            $value instanceof CarbonPeriod ? $value->getStartDate() : null,
                            $args
                        ),
                    ],
                    'endDate' => [
                        'type' => Type::string(),
                        'args' => [$formatArg],
                        'resolve' => static fn (mixed $value = null, array $args = []): ?string => self::format(
                            $value instanceof CarbonPeriod ? $value->getEndDate() : null,
                            $args
                        ),
                    ],
                ],
            ]);
        }

        return self::$instance;
    }

    private static function format(mixed $date, array $args): ?string
    {
        if (!$date instanceof CarbonInterface) {
            return null;
        }

        $format = $args[self::FORMAT] ?? null;

        return $format === null ? (string) $date : $date->format($format);
    }
}
