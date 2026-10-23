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

use function array_is_list;
use function array_key_exists;
use function array_keys;
use function array_pop;
use function explode;
use function in_array;
use function is_array;
use function preg_match;
use function str_contains;

/**
 * A stored configuration as a proposal may see and carry it.
 *
 * @internal
 */
final class ConfigDocument
{
    /**
     * Bookkeeping the editor never binds and a draft must never carry: it changes on every
     * save, so a draft holding it would report a change nobody made.
     */
    public const array VOLATILE_GENERAL = ['modificationDate', 'createDate', 'creationDate', 'writeable'];

    public static function isValidName(string $name): bool
    {
        return preg_match('/^[[:alnum:]][[:alnum:]_-]*$/', $name) === 1;
    }

    /**
     * The document without bookkeeping and without anything withheld.
     *
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    public static function readable(array $document, ConfigProposalPolicyInterface $policy): array
    {
        foreach (self::VOLATILE_GENERAL as $volatile) {
            unset($document['general'][$volatile]);
        }

        foreach ($policy->withheld() as $path) {
            $document = self::without($document, $path);
        }

        return $document;
    }

    /**
     * The withheld paths only: what Change Control keeps out of a draft by itself.
     *
     * @return list<string>
     */
    public static function withheldSections(ConfigProposalPolicyInterface $policy): array
    {
        $sections = [];
        foreach ($policy->withheld() as $path) {
            if (!str_contains($path, '.')) {
                $sections[] = $path;
            }
        }

        return $sections;
    }

    /**
     * Sections that are not part of this kind of configuration at all — a document written
     * from memory rather than read.
     *
     * @param array<string, mixed> $proposed
     *
     * @return list<string>
     */
    public static function unknownSections(array $proposed, ConfigProposalPolicyInterface $policy): array
    {
        $known = [...$policy->proposable(), ...self::withheldSections($policy)];
        $unknown = [];
        foreach (array_keys($proposed) as $section) {
            if (!in_array((string) $section, $known, true)) {
                $unknown[] = (string) $section;
            }
        }

        return $unknown;
    }

    /**
     * Withheld paths the proposal carries anyway.
     *
     * @param array<string, mixed> $proposed
     *
     * @return list<string>
     */
    public static function refusedPaths(array $proposed, ConfigProposalPolicyInterface $policy): array
    {
        $refused = [];
        foreach ($policy->withheld() as $path) {
            if (self::has($proposed, $path)) {
                $refused[] = $path;
            }
        }

        return $refused;
    }

    /**
     * The stored document with the proposal laid over it, section by section and then key by
     * key. A list replaces wholesale — half a list is not a thing a reviewer can read.
     *
     * @param array<string, mixed> $stored
     * @param array<string, mixed> $proposed
     *
     * @return array<string, mixed>
     */
    public static function fold(array $stored, array $proposed): array
    {
        $state = $stored;

        foreach ($proposed as $section => $value) {
            $section = (string) $section;
            if (!is_array($value) || array_is_list($value) || !is_array($state[$section] ?? null)) {
                $state[$section] = $value;

                continue;
            }

            foreach ($value as $key => $child) {
                $state[$section][$key] = $child;
            }
        }

        return $state;
    }

    /**
     * The withheld paths as storage has them: they never rode the change set, and a save
     * writes the whole document, so dropping them would erase them.
     *
     * @param array<string, mixed> $configuration
     * @param array<string, mixed> $stored
     *
     * @return array<string, mixed>
     */
    public static function withStored(
        array $configuration,
        array $stored,
        ConfigProposalPolicyInterface $policy,
    ): array {
        foreach ($policy->withheld() as $path) {
            $configuration = self::without($configuration, $path);
            if (self::has($stored, $path)) {
                $configuration = self::with($configuration, $path, self::at($stored, $path));
            }
        }

        return $configuration;
    }

    /**
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    private static function without(array $document, string $path): array
    {
        $segments = explode('.', $path);
        $leaf = array_pop($segments);
        $node = &$document;
        foreach ($segments as $segment) {
            if (!is_array($node[$segment] ?? null)) {
                return $document;
            }
            $node = &$node[$segment];
        }
        unset($node[$leaf]);

        return $document;
    }

    /**
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    private static function with(array $document, string $path, mixed $value): array
    {
        $node = &$document;
        foreach (explode('.', $path) as $segment) {
            if (!is_array($node)) {
                $node = [];
            }
            $node = &$node[$segment];
        }
        $node = $value;

        return $document;
    }

    /**
     * @param array<string, mixed> $document
     */
    private static function has(array $document, string $path): bool
    {
        $node = $document;
        foreach (explode('.', $path) as $segment) {
            if (!is_array($node) || !array_key_exists($segment, $node)) {
                return false;
            }
            $node = $node[$segment];
        }

        return true;
    }

    /**
     * @param array<string, mixed> $document
     */
    private static function at(array $document, string $path): mixed
    {
        $node = $document;
        foreach (explode('.', $path) as $segment) {
            $node = is_array($node) ? ($node[$segment] ?? null) : null;
        }

        return $node;
    }
}
