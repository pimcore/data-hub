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

use function array_map;
use function floor;
use function is_array;
use function is_finite;
use function is_float;
use function is_string;
use const PHP_INT_MAX;
use const PHP_INT_MIN;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigDocument;
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
     * The mutation entities whose input fields change while they may create or update: the
     * fields are what those grants write, so a changed list widens write access as a new flag
     * would.
     *
     * @param array<string, mixed> $document
     * @param array<string, mixed> $stored
     *
     * @return list<string>
     */
    public function changedWriteInputs(array $document, array $stored): array
    {
        $entities = $document['schema']['mutationEntities'] ?? null;
        $entities = is_array($entities) ? $entities : [];
        $before = $stored['schema']['mutationEntities'] ?? null;
        $before = is_array($before) ? $before : [];

        $changed = [];
        foreach ($entities as $key => $entity) {
            $storedEntity = $before[$key] ?? null;
            if (!is_array($entity) || !is_array($storedEntity)
                || !$this->writesInputs($entity) || !$this->writesInputs($storedEntity)) {
                continue;
            }

            if ($this->inputsOf($entity) !== $this->inputsOf($storedEntity)) {
                $changed[] = sprintf('schema.mutationEntities.%s columnConfig', $key);
            }
        }

        return $changed;
    }

    /**
     * @param array<mixed> $entity
     */
    private function inputsOf(array $entity): mixed
    {
        $columns = $entity['columnConfig'] ?? null;

        return $this->integral(is_array($columns) ? ConfigDocument::normalized($columns) : $columns);
    }

    /**
     * 200.0 and 200 are the same setting; a JSON round trip may turn one into the other.
     */
    private function integral(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map($this->integral(...), $value);
        }

        if (is_float($value) && is_finite($value) && floor($value) === $value
            && $value >= PHP_INT_MIN && $value < PHP_INT_MAX) {
            return (int) $value;
        }

        return $value;
    }

    /**
     * @param array<mixed> $entity
     */
    private function writesInputs(array $entity): bool
    {
        foreach ($this->flagsOf($entity, '') as $flag) {
            if ($flag === ' create' || $flag === ' update') {
                return true;
            }
        }

        return false;
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
