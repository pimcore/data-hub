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

use GraphQL\Error\Error;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils as GraphQLUtils;
use Safe\Exceptions\JsonException;

class JsonType extends ScalarType
{
    public $description /** @lang Markdown */
        = 'Arbitrary data encoded in JavaScript Object Notation. See https://www.json.org.';

    public function serialize(mixed $value): string
    {
        return \Safe\json_encode($value);
    }

    public function parseValue(mixed $value)
    {
        return $this->decodeJSON($value);
    }

    public function parseLiteral(mixed $valueNode, ?array $variables = null)
    {
        if (! property_exists($valueNode, 'value')) {
            throw new Error(
                'Can only parse literals that contain a value, got ' . GraphQLUtils::printSafeJson($valueNode)
            );
        }

        return $this->decodeJSON($valueNode->value);
    }

    /**
     * Try to decode a user-given JSON value.
     *
     * @param mixed $value A user given JSON
     *
     * @throws Error
     *
     * @return mixed The decoded value
     */
    protected function decodeJSON(mixed $value): mixed
    {
        try {
            $decoded = \Safe\json_decode($value);
        } catch (JsonException $jsonException) {
            throw new Error(
                $jsonException->getMessage()
            );
        }

        return $decoded;
    }
}
