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

namespace Pimcore\Bundle\DataHubBundle\Proposal\GraphQl;

use function implode;
use function in_array;
use function is_array;
use function is_string;
use Pimcore\Model\Element\Service as ElementService;
use function sprintf;

/**
 * The save keeps a workspace only when its path resolves to an element and reads its path
 * without a guard, so a reviewed workspace on a missing path would vanish on approval.
 *
 * @internal
 */
final readonly class GraphQlWorkspacePaths
{
    private const array WORKSPACE_TYPES = ['document', 'asset', 'object'];

    /**
     * @param array<string, mixed> $document
     *
     * @return list<string>
     */
    public function unresolved(array $document): array
    {
        $problems = [];
        $workspaces = is_array($document['workspaces'] ?? null) ? $document['workspaces'] : [];
        foreach ($workspaces as $type => $entries) {
            if (!in_array($type, self::WORKSPACE_TYPES, true)) {
                $problems[] = sprintf(
                    'workspaces.%s is not a workspace type; use %s.',
                    $type,
                    implode(', ', self::WORKSPACE_TYPES),
                );

                continue;
            }

            foreach (is_array($entries) ? $entries : [] as $index => $entry) {
                $problem = $this->unresolvedEntry((string) $type, $index, $entry);
                if ($problem !== null) {
                    $problems[] = $problem;
                }
            }
        }

        return $problems;
    }

    private function unresolvedEntry(string $type, int|string $index, mixed $entry): ?string
    {
        $path = is_array($entry) ? ($entry['cpath'] ?? null) : null;
        if (!is_string($path) || $path === '') {
            return sprintf(
                'workspaces.%s entry %s needs "cpath", the full path of the %s it grants.',
                $type,
                $index,
                $type,
            );
        }

        if (ElementService::getElementByPath($type, $path) === null) {
            return sprintf(
                'workspaces.%s %s: no %s exists at this path, and a workspace on a missing '
                . 'path is dropped when the configuration is saved.',
                $type,
                $path,
                $type,
            );
        }

        return null;
    }
}
