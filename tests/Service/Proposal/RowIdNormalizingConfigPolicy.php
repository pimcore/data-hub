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

namespace Pimcore\Bundle\DataHubBundle\Tests\Service\Proposal;

use function is_array;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigStateNormalizerInterface;

/**
 * An adapter whose editor mints a `rowId` for every workspace row: reviews compare rows without
 * it, approvals put the stored one back by the row's path.
 */
final class RowIdNormalizingConfigPolicy extends RecordingConfigPolicy implements ConfigStateNormalizerInterface
{
    /** @var list<array<string, mixed>> the stored configuration each restored() call was given */
    public array $restoredFrom = [];

    public function comparable(array $state): array
    {
        foreach ($state['workspaces']['object'] ?? [] as $i => $row) {
            unset($state['workspaces']['object'][$i]['rowId']);
        }

        return $state;
    }

    public function restored(array $approved, array $stored): array
    {
        $this->restoredFrom[] = $stored;
        $ids = [];
        foreach ($stored['workspaces']['object'] ?? [] as $row) {
            if (is_array($row) && isset($row['cpath'], $row['rowId'])) {
                $ids[$row['cpath']] = $row['rowId'];
            }
        }

        foreach ($approved['workspaces']['object'] ?? [] as $i => $row) {
            if (isset($ids[$row['cpath'] ?? null])) {
                $approved['workspaces']['object'][$i]['rowId'] = $ids[$row['cpath']];
            }
        }

        return $approved;
    }
}
