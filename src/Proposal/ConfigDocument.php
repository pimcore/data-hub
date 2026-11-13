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
use function array_slice;
use function count;
use function explode;
use function implode;
use function in_array;
use InvalidArgumentException;
use function is_array;
use function ksort;
use function preg_match;
use const SORT_STRING;
use function sprintf;
use function str_contains;
use function str_ends_with;
use function str_starts_with;
use function substr;
use function substr_count;

/**
 * A stored configuration as a proposal may see and carry it.
 *
 * @internal
 */
final class ConfigDocument
{
    /**
     * Marks a proposal made while nothing was stored under its name. Change Control hands the
     * subject only the merged state, so this is how an approval tells a create from a change.
     */
    public const string CREATE_MARKER = '__new';

    /**
     * Bookkeeping the editor never binds and a draft must never carry: it changes on every
     * save, so a draft holding it would report a change nobody made.
     */
    public const array VOLATILE_GENERAL = ['modificationDate', 'createDate', 'creationDate', 'writeable'];

    /** ends the last segment of a withheld path to withhold every key that starts with the rest */
    public const string WILDCARD = '*';

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
            foreach (self::matches($document, self::segments($path)) as $match) {
                $document = self::without($document, $match);
            }
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
            self::segments($path);
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
            foreach (self::matches($proposed, self::segments($path)) as $match) {
                $refused[] = implode('.', $match);
            }
        }

        return $refused;
    }

    /**
     * Whether a leaf address, or a value sent whole under it, reaches into a withheld path.
     *
     * @param list<string> $address
     */
    public static function withholds(array $address, mixed $value, ConfigProposalPolicyInterface $policy): bool
    {
        foreach ($policy->withheld() as $path) {
            $withheld = self::segments($path);
            $depth = count($withheld);
            if (count($address) >= $depth) {
                if (self::covers($withheld, array_slice($address, 0, $depth))) {
                    return true;
                }

                continue;
            }
            if (array_slice($withheld, 0, count($address)) === $address
                && is_array($value)
                && self::matches($value, array_slice($withheld, count($address))) !== []) {
                return true;
            }
        }

        return false;
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
            $segments = self::segments($path);
            foreach (self::matches($configuration, $segments) as $match) {
                $configuration = self::without($configuration, $match);
            }
            foreach (self::matches($stored, $segments) as $match) {
                $configuration = self::with($configuration, $match, self::at($stored, $match));
            }
        }

        return $configuration;
    }

    /**
     * Compared strictly, "01" and "1" differ; a map's key order is no change.
     *
     * @param array<array-key, mixed> $document
     *
     * @return array<array-key, mixed>
     */
    public static function normalized(array $document): array
    {
        if (!array_is_list($document)) {
            ksort($document, SORT_STRING);
        }
        foreach ($document as $key => $value) {
            if (is_array($value)) {
                $document[$key] = self::normalized($value);
            }
        }

        return $document;
    }

    /**
     * A withheld path as segments. Only the last segment of a path below a section may end in
     * the wildcard; anywhere else it would be read literally and withhold nothing, so it is
     * refused instead.
     *
     * @return list<string>
     */
    private static function segments(string $path): array
    {
        $segments = explode('.', $path);
        $wildcards = substr_count($path, self::WILDCARD);
        if ($wildcards > 1
            || ($wildcards === 1 && (count($segments) < 2 || !str_ends_with($path, self::WILDCARD)))) {
            throw new InvalidArgumentException(sprintf(
                'Withheld path "%s" is not supported: only the last segment of a path below a section '
                . 'may end in "%s".',
                $path,
                self::WILDCARD,
            ));
        }

        return $segments;
    }

    /**
     * The keys of the document a withheld path names: itself, or with a wildcard every key
     * under its parent that starts with the rest of the last segment.
     *
     * @param array<array-key, mixed> $document
     * @param list<string> $withheld
     *
     * @return list<list<string>>
     */
    private static function matches(array $document, array $withheld): array
    {
        $leaf = array_pop($withheld);
        if ($leaf === null) {
            return [];
        }
        if (!str_ends_with($leaf, self::WILDCARD)) {
            return self::has($document, [...$withheld, $leaf]) ? [[...$withheld, $leaf]] : [];
        }

        $parent = self::at($document, $withheld);
        if (!is_array($parent)) {
            return [];
        }

        $matches = [];
        foreach (array_keys($parent) as $key) {
            if (self::covers([$leaf], [(string) $key])) {
                $matches[] = [...$withheld, (string) $key];
            }
        }

        return $matches;
    }

    /**
     * @param list<string> $withheld
     * @param list<string> $address as long as $withheld
     */
    private static function covers(array $withheld, array $address): bool
    {
        foreach ($withheld as $index => $segment) {
            $key = $address[$index] ?? null;
            if ($key === null) {
                return false;
            }
            $matches = str_ends_with($segment, self::WILDCARD)
                ? str_starts_with($key, substr($segment, 0, -1))
                : $key === $segment;
            if (!$matches) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<string, mixed> $document
     * @param list<string> $segments
     *
     * @return array<string, mixed>
     */
    private static function without(array $document, array $segments): array
    {
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
     * @param list<string> $segments
     *
     * @return array<string, mixed>
     */
    private static function with(array $document, array $segments, mixed $value): array
    {
        $node = &$document;
        foreach ($segments as $segment) {
            if (!is_array($node)) {
                $node = [];
            }
            $node = &$node[$segment];
        }
        $node = $value;

        return $document;
    }

    /**
     * @param array<array-key, mixed> $document
     * @param list<string> $segments
     */
    private static function has(array $document, array $segments): bool
    {
        $node = $document;
        foreach ($segments as $segment) {
            if (!is_array($node) || !array_key_exists($segment, $node)) {
                return false;
            }
            $node = $node[$segment];
        }

        return true;
    }

    /**
     * @param array<array-key, mixed> $document
     * @param list<string> $segments
     */
    private static function at(array $document, array $segments): mixed
    {
        $node = $document;
        foreach ($segments as $segment) {
            $node = is_array($node) ? ($node[$segment] ?? null) : null;
        }

        return $node;
    }
}
