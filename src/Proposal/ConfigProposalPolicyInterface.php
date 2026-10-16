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
 * What one adapter lets an agent propose about its configurations, and how a reviewed one is
 * written back.
 *
 * Free of any Change Control or MCP symbol on purpose: both are optional peers, and this is the
 * one rule the review lane and the authoring tools have to agree on.
 */
interface ConfigProposalPolicyInterface
{
    /** the adapter type configurations of this kind are stored under */
    public function configType(): string;

    /** the Change Control subject type a proposal records against */
    public function subjectType(): string;

    /** how a message names one configuration: "webhook configuration" */
    public function noun(): string;

    /** the MCP tool an agent reads a configuration with, named in refusals */
    public function readToolName(): string;

    /**
     * Top-level sections a proposal may carry.
     *
     * @return list<string>
     */
    public function proposable(): array;

    /**
     * Paths a proposal may never carry, never reviewed and never merged: a whole section, or
     * `section.key` inside a proposable one. They are withheld from the agent too.
     *
     * @return list<string>
     */
    public function withheld(): array;

    /** why the withheld paths are refused, told to the agent that sent one */
    public function withheldReason(): string;

    /**
     * Editor tab => the sections it shows, in the order the editor lays them out.
     *
     * @return array<string, list<string>>
     */
    public function slots(): array;

    /**
     * What this adapter refuses in the configuration a proposal would produce: names the
     * installation does not have, essentials a new configuration lacks.
     *
     * @param array<string, mixed> $state
     *
     * @return list<string>
     */
    public function problems(array $state, bool $isNew): array;

    /**
     * What the list tool reports per configuration beyond its name, group, description and
     * whether it is active.
     *
     * @param array<string, mixed> $configuration
     *
     * @return array<string, mixed>
     */
    public function summarize(array $configuration): array;

    /**
     * Writes a reviewed configuration through the adapter's own save path, so its validation
     * and side effects run as they would for the editor.
     *
     * @param array<string, mixed> $configuration
     */
    public function save(string $name, array $configuration): void;
}
