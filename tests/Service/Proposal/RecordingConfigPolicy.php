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

use Pimcore\Bundle\DataHubBundle\Proposal\ConfigProposalPolicyInterface;
use RuntimeException;

/**
 * A webhook-like adapter policy that records what it is asked to save.
 */
final class RecordingConfigPolicy implements ConfigProposalPolicyInterface
{
    /** @var list<array{string, array<string, mixed>}> */
    public array $saved = [];

    /**
     * @param list<string> $problems
     */
    public function __construct(
        private readonly array $problems = [],
        private readonly bool $saveFails = false,
    ) {
    }

    public function configType(): string
    {
        return 'webhooks';
    }

    public function subjectType(): string
    {
        return 'data-hub-webhook-config';
    }

    public function noun(): string
    {
        return 'webhook configuration';
    }

    public function readToolName(): string
    {
        return 'webhook_config_get';
    }

    public function proposable(): array
    {
        return ['general', 'eventsSchema', 'workspaces'];
    }

    public function withheld(): array
    {
        return ['security', 'general.apiKey'];
    }

    public function withheldReason(): string
    {
        return 'Credentials are set by a person.';
    }

    public function slots(): array
    {
        return ['general' => ['general'], 'events' => ['eventsSchema'], 'workspaces' => ['workspaces']];
    }

    public function problems(array $state, bool $isNew): array
    {
        return $this->problems;
    }

    public function summarize(array $configuration): array
    {
        return ['events' => $configuration['eventsSchema']['events'] ?? []];
    }

    public function save(string $name, array $configuration): void
    {
        if ($this->saveFails) {
            throw new RuntimeException('The adapter rejected the configuration.');
        }

        $this->saved[] = [$name, $configuration];
    }
}
