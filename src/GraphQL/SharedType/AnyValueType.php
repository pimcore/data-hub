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

use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\AST;

/**
 * Passes a value of any shape through, the way the editor sends it; the key's own type converts it.
 *
 * @internal
 */
final class AnyValueType extends ScalarType
{
    public function serialize(mixed $value): mixed
    {
        return $value;
    }

    public function parseValue(mixed $value): mixed
    {
        return $value;
    }

    public function parseLiteral(Node $valueNode, ?array $variables = null): mixed
    {
        return AST::valueFromASTUntyped($valueNode, $variables);
    }
}
