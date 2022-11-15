<?php declare(strict_types=1);

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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\SharedType;

use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils as GraphQLUtils;

class JsonType extends ScalarType
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
