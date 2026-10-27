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

use function array_filter;
use const ARRAY_FILTER_USE_BOTH;
use const ARRAY_FILTER_USE_KEY;
use function array_is_list;
use function array_key_exists;
use function array_slice;
use function count;
use function explode;
use function in_array;
use function is_array;
use Pimcore\Bundle\ChangeControlBundle\Hydrator\DetailHydratorInterface;
use Pimcore\Bundle\ChangeControlBundle\Hydrator\SlotDetail;
use Pimcore\Bundle\ChangeControlBundle\Merge\LeafPath;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectRef;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigDocument;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigProposalPolicyInterface;
use Pimcore\Model\UserInterface;

/**
 * Groups a configuration's leaves by the editor tab that shows them.
 */
final readonly class ConfigDetailHydrator implements DetailHydratorInterface
{
    public function __construct(private ConfigProposalPolicyInterface $policy)
    {
    }

    public function hydrate(SubjectRef $subject, array $tree, array $provisional = []): array
    {
        $slots = [];

        foreach ($this->policy->slots() as $slot => $sections) {
            $values = [];
            foreach ($sections as $section) {
                if (array_key_exists($section, $tree)) {
                    $values += $this->flatten([$section], $tree[$section]);
                }
            }
            if ($values !== []) {
                $slots[$slot] = new SlotDetail(SlotDetail::SHAPE_RECORD, $values);
            }
        }

        // a section no tab names still has to reach the reviewer
        foreach ($tree as $key => $value) {
            $key = (string) $key;
            if ($key !== ConfigDocument::CREATE_MARKER && !$this->isKnown($key)) {
                $slots[$key] = new SlotDetail(SlotDetail::SHAPE_RECORD, $this->flatten([$key], $value));
            }
        }

        return $slots;
    }

    public function dehydrate(SubjectRef $subject, array $patch, array $proposed, ?UserInterface $user = null): array
    {
        // a withheld leaf is never stored through a review: an address naming one, or a section
        // sent whole that holds one, is dropped, so Change Control refuses the amendment; the
        // create marker is stored only as the proposal sets it, so a review can never clear it
        $patch = array_filter(
            $patch,
            function (mixed $value, int|string $address): bool {
                $segments = LeafPath::split((string) $address);
                if ($segments[0] === ConfigDocument::CREATE_MARKER) {
                    return $segments === [ConfigDocument::CREATE_MARKER] && $value === true;
                }

                return !$this->isWithheld($segments, $value);
            },
            ARRAY_FILTER_USE_BOTH,
        );

        // a create has no tree yet: the patch IS the document, every section of it
        if ($proposed === []) {
            return $patch;
        }

        // the surface is read-only, so a patch can only name a section the tree already has;
        // anything else is a rendering artefact and never becomes a stored leaf
        return array_filter(
            $patch,
            static fn (int|string $address): bool => array_key_exists(LeafPath::split((string) $address)[0], $proposed),
            ARRAY_FILTER_USE_KEY,
        );
    }

    /**
     * @param list<string> $segments
     */
    private function isWithheld(array $segments, mixed $value): bool
    {
        foreach ($this->policy->withheld() as $path) {
            $withheld = explode('.', $path);
            if (array_slice($segments, 0, count($withheld)) === $withheld) {
                return true;
            }
            if (array_slice($withheld, 0, count($segments)) === $segments
                && self::holds($value, array_slice($withheld, count($segments)))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<string> $path
     */
    private static function holds(mixed $value, array $path): bool
    {
        foreach ($path as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return false;
            }
            $value = $value[$key];
        }

        return true;
    }

    /**
     * Flattens to scalar leaves. A list stays whole: its members have no stable address, and
     * a reviewer reads "the subscribed events changed", not "event 2 changed".
     *
     * @param list<string> $prefix
     *
     * @return array<string, mixed>
     */
    private function flatten(array $prefix, mixed $value): array
    {
        if (!is_array($value) || $value === [] || array_is_list($value)) {
            return [LeafPath::join($prefix) => $value];
        }

        $leaves = [];
        foreach ($value as $key => $child) {
            $leaves += $this->flatten([...$prefix, (string) $key], $child);
        }

        return $leaves;
    }

    private function isKnown(string $section): bool
    {
        foreach ($this->policy->slots() as $sections) {
            if (in_array($section, $sections, true)) {
                return true;
            }
        }

        return false;
    }
}
