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
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeInterface\CsFeature;

/**
 * One shared type for every table key: the rows by position, the column names of the key configuration are not
 * published.
 *
 * @internal
 */
final class TableType extends ObjectType
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
            $fields['rows'] = [
                'type' => Type::listOf(Type::listOf(Type::string())),
                'resolve' => static fn (mixed $value = null): ?array => $value instanceof FeatureDescriptor
                    ? self::rows($value->getValue())
                    : null,
            ];

            self::$instance = new static([
                'name' => 'csFeatureTable',
                'interfaces' => [CsFeature::getInstance()],
                'fields' => $fields,
            ]);
        }

        return self::$instance;
    }

    /**
     * The stored rows by position: a key with an active column configuration keeps them by column name, and the
     * names belong to that one key, not to the type every table key is published as.
     *
     * @return array<int, array<int, ?string>>|null
     */
    public static function rows(mixed $value): ?array
    {
        if (!is_array($value)) {
            return null;
        }

        $rows = [];
        foreach ($value as $row) {
            $cells = is_array($row) ? array_values($row) : [$row];
            $rows[] = array_map(
                static fn (mixed $cell): ?string => is_scalar($cell) ? (string) $cell : null,
                $cells
            );
        }

        return $rows;
    }
}
