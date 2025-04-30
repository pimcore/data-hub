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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\SharedType;

use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils as GraphQLUtils;

final class JsonType extends ScalarType
{
    public function serialize(mixed $value): string
    {
        return json_encode($value);
    }

    public function parseValue(mixed $value): mixed
    {
        return json_decode($value);
    }

    public function parseLiteral(mixed $valueNode, ?array $variables = null): mixed
    {
        if (! property_exists($valueNode, 'value')) {
            throw new \Exception('Can only parse objects with a value property. Input: ' . GraphQLUtils::printSafeJson($valueNode));
        }

        return json_decode($valueNode->value);
    }
}
