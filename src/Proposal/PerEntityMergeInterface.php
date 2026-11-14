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

namespace Pimcore\Bundle\DataHubBundle\Proposal;

/**
 * Opt-in for a policy whose configuration keeps maps of one entry per entity. Under each of
 * these paths a proposal replaces only the entities it carries, keeps the ones it omits and
 * removes one it sends as null, so an agent never has to send back what it did not change.
 */
interface PerEntityMergeInterface
{
    /**
     * `section.key` paths of maps keyed by entity, e.g. `schema.queryEntities`.
     *
     * @return list<string>
     */
    public function perEntityPaths(): array;
}
