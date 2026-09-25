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

namespace Pimcore\Bundle\DataHubBundle\Proposal\ChangeControl;

use function array_is_list;
use function is_array;
use Pimcore\Bundle\ChangeControlBundle\Merge\NodeKind;
use Pimcore\Bundle\ChangeControlBundle\Merge\StateShapeInterface;

/**
 * Keyed maps merge key by key; a list merges whole, since its members have no stable address.
 */
final readonly class ConfigStateShape implements StateShapeInterface
{
    public function kindOf(array $segments, mixed $value): NodeKind
    {
        return is_array($value) && $value !== [] && !array_is_list($value)
            ? NodeKind::Container
            : NodeKind::Atomic;
    }
}
