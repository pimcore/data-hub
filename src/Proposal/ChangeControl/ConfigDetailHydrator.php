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
use const ARRAY_FILTER_USE_KEY;
use function array_is_list;
use function array_key_exists;
use function in_array;
use function is_array;
use Pimcore\Bundle\ChangeControlBundle\Hydrator\DetailHydratorInterface;
use Pimcore\Bundle\ChangeControlBundle\Hydrator\SlotDetail;
use Pimcore\Bundle\ChangeControlBundle\Merge\LeafPath;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectRef;
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

    public function hydrate(SubjectRef $subject, array $tree): array
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
            if (!$this->isKnown((string) $key)) {
                $slots[(string) $key] = new SlotDetail(SlotDetail::SHAPE_RECORD, $this->flatten([(string) $key], $value));
            }
        }

        return $slots;
    }

    public function dehydrate(SubjectRef $subject, array $patch, array $proposed, ?UserInterface $user = null): array
    {
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
