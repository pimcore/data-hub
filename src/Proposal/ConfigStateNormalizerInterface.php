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
 * Optional capability of a {@see ConfigProposalPolicyInterface}: an adapter whose editor writes
 * values on a save nobody changed (ids minted for list rows) drops them before a review compares
 * states, and puts them back before an approval is saved. A policy without it is compared and
 * saved as it is.
 *
 * Separate from the policy so existing implementations stay valid.
 */
interface ConfigStateNormalizerInterface
{
    /**
     * The state as a review reads and compares it: the stored state at propose and at approve,
     * and the proposal's own. Never what is saved.
     *
     * @param array<string, mixed> $state
     *
     * @return array<string, mixed>
     */
    public function comparable(array $state): array;

    /**
     * The approved state as it is saved: what comparable() dropped, put back from the
     * configuration stored now (empty for a new one).
     *
     * @param array<string, mixed> $approved
     * @param array<string, mixed> $stored
     *
     * @return array<string, mixed>
     */
    public function restored(array $approved, array $stored): array;
}
