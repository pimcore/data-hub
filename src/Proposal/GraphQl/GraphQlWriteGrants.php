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

use function is_array;
use function is_string;
use function sprintf;
use function strtolower;

/**
 * Every write permission a GraphQL configuration document grants, as a readable address.
 *
 * @internal
 */
final readonly class GraphQlWriteGrants
{
    // the Studio save reads the generic types' flags under either name
    private const array WRITE_FLAGS = [
        'create' => 'createAllowed',
        'update' => 'updateAllowed',
        'delete' => 'deleteAllowed',
    ];

    /**
     * @param array<string, mixed> $document
     *
     * @return list<string>
     */
    public function of(array $document): array
    {
        $grants = [];

        $workspaces = is_array($document['workspaces'] ?? null) ? $document['workspaces'] : [];
        foreach ($workspaces as $kind => $entries) {
            foreach (is_array($entries) ? $entries : [] as $entry) {
                $path = is_array($entry) && is_string($entry['cpath'] ?? null) ? $entry['cpath'] : '';
                $grants = [...$grants, ...$this->flagsOf($entry, sprintf('workspaces.%s %s', $kind, $path))];
            }
        }

        foreach (['mutationEntities', 'specialEntities'] as $kind) {
            $entities = is_array($document['schema'][$kind] ?? null) ? $document['schema'][$kind] : [];
            foreach ($entities as $key => $entity) {
                $grants = [...$grants, ...$this->flagsOf($entity, sprintf('schema.%s.%s', $kind, $key))];
            }
        }

        return $grants;
    }

    /**
     * @return list<string>
     */
    private function flagsOf(mixed $entry, string $address): array
    {
        if (!is_array($entry)) {
            return [];
        }

        // any truthy value counts: the save casts these to bool, the mutation entities keep them as sent;
        // and under any case, as the workspace save calls set<Key>() and PHP method names ignore case
        $granted = [];
        foreach ($entry as $key => $value) {
            if ((bool) $value) {
                $granted[strtolower((string) $key)] = true;
            }
        }

        $flags = [];
        foreach (self::WRITE_FLAGS as $flag => $alias) {
            if (isset($granted[$flag]) || isset($granted[strtolower($alias)])) {
                $flags[] = $address . ' ' . $flag;
            }
        }

        return $flags;
    }
}
