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

use Codeception\Test\Unit;
use Pimcore\Bundle\ChangeControlBundle\Merge\NodeKind;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectHandlerInterface;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectRef;
use Pimcore\Bundle\DataHubBundle\Proposal\ChangeControl\ConfigDetailHydrator;
use Pimcore\Bundle\DataHubBundle\Proposal\ChangeControl\ConfigStateShape;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigDocument;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigProposalPolicyInterface;
use Symfony\Component\Uid\Uuid;

/**
 * The review lane's pure pieces. Change Control is an optional peer, so these skip rather than
 * fail where it is not installed.
 */
class ConfigChangeControlTest extends Unit
{
    protected function setUp(): void
    {
        if (!interface_exists(SubjectHandlerInterface::class)) {
            $this->markTestSkipped('Change Control is not installed.');
        }
    }

    /**
     * The engine's structural default reads a keyed array carrying `type` or `id` as an element
     * reference and makes it one leaf. A configuration's `general` has such a key and is none.
     */
    public function testAKeyedSectionCarryingTypeIsStillWalkedIntoLeaves(): void
    {
        $this->assertSame(
            NodeKind::Container,
            (new ConfigStateShape())->kindOf(['general'], ['active' => true, 'type' => 'webhooks', 'path' => null]),
        );
    }

    public function testAListStaysAtomic(): void
    {
        $shape = new ConfigStateShape();

        $this->assertSame(NodeKind::Atomic, $shape->kindOf(['schema', 'columns'], ['a', 'b']));
        $this->assertSame(NodeKind::Atomic, $shape->kindOf(['schema', 'columns'], []));
        $this->assertSame(NodeKind::Atomic, $shape->kindOf(['general', 'active'], true));
    }

    public function testHydratorGroupsSectionsIntoTheEditorsTabs(): void
    {
        $slots = $this->hydrator()->hydrate($this->ref(), [
            'general' => ['active' => true, 'description' => 'd'],
            'eventsSchema' => ['events' => ['pimcore.dataobject.postUpdate']],
            'workspaces' => ['object' => []],
        ]);

        $this->assertSame(['general', 'events', 'workspaces'], array_keys($slots));
        $this->assertArrayHasKey('general.active', $slots['general']->values);
        // a list is one address: "the subscribed events changed", not "event 2 changed"
        $this->assertSame(['pimcore.dataobject.postUpdate'], $slots['events']->values['eventsSchema.events']);
    }

    public function testASectionNoTabNamesBecomesItsOwnSlot(): void
    {
        $slots = $this->hydrator()->hydrate($this->ref(), ['somethingNew' => ['a' => 1]]);

        $this->assertSame(1, $slots['somethingNew']->values['somethingNew.a']);
    }

    public function testTheCreateMarkerIsNoSlotOfTheReview(): void
    {
        $slots = $this->hydrator()->hydrate($this->ref(), [
            'general' => ['active' => false],
            ConfigDocument::CREATE_MARKER => true,
        ]);

        $this->assertSame(['general'], array_keys($slots));
    }

    public function testAReviewCannotAmendTheCreateMarker(): void
    {
        $proposed = ['general' => ['active' => false], ConfigDocument::CREATE_MARKER => true];

        $this->assertSame(
            ['general.active' => true],
            $this->hydrator()->dehydrate(
                $this->ref(),
                ['general.active' => true, ConfigDocument::CREATE_MARKER => false],
                $proposed,
            ),
        );
        $this->assertSame([], $this->hydrator()->dehydrate($this->ref(), [ConfigDocument::CREATE_MARKER => false], []));
    }

    public function testACreateDehydratesToTheWholePatch(): void
    {
        $patch = ['general.active' => false, 'eventsSchema.events' => ['a']];

        $this->assertSame($patch, $this->hydrator()->dehydrate($this->ref(), $patch, []));
    }

    public function testAChangeDehydratesOnlyToSectionsTheProposalHas(): void
    {
        $this->assertSame(
            ['general.active' => false],
            $this->hydrator()->dehydrate(
                $this->ref(),
                ['general.active' => false, 'workspaces.object' => []],
                ['general' => ['active' => true]],
            ),
        );
    }

    public function testAnAmendmentOfAWithheldPathIsNeverStored(): void
    {
        $hydrator = new ConfigDetailHydrator(new RecordingConfigPolicy());
        $patch = [
            'general.apiKey' => 'x',
            'security.method' => 'none',
            'general.apiKeyHint' => 'kept',
            'general.active' => false,
        ];
        $kept = ['general.apiKeyHint' => 'kept', 'general.active' => false];

        $this->assertSame($kept, $hydrator->dehydrate($this->ref(), $patch, ['general' => ['active' => true]]));
        $this->assertSame($kept, $hydrator->dehydrate($this->ref(), $patch, []));
    }

    public function testAnAmendmentOfASectionHoldingAWithheldPathIsNeverStored(): void
    {
        $hydrator = new ConfigDetailHydrator(new RecordingConfigPolicy());
        $patch = [
            'general' => ['apiKey' => 'x', 'active' => false],
            'workspaces' => ['object' => []],
        ];
        $kept = ['workspaces' => ['object' => []]];
        $existing = ['general' => ['active' => true], 'workspaces' => ['object' => []]];

        // dropping the address makes Change Control refuse it, as it refuses a withheld leaf
        $this->assertSame($kept, $hydrator->dehydrate($this->ref(), $patch, $existing));
        $this->assertSame($kept, $hydrator->dehydrate($this->ref(), $patch, []));
        $this->assertSame([], $hydrator->dehydrate($this->ref(), ['general' => ['apiKey' => 'x']], $existing));
    }

    public function testASectionWithoutTheWithheldPathIsStoredWhole(): void
    {
        $hydrator = new ConfigDetailHydrator(new RecordingConfigPolicy());
        $patch = ['general' => ['apiKeyHint' => 'kept', 'active' => false], 'security' => 'none'];

        $this->assertSame(
            ['general' => $patch['general']],
            $hydrator->dehydrate($this->ref(), $patch, ['general' => []])
        );
    }

    private function hydrator(): ConfigDetailHydrator
    {
        $policy = $this->createStub(ConfigProposalPolicyInterface::class);
        $policy->method('slots')->willReturn([
            'general' => ['general'],
            'events' => ['eventsSchema'],
            'workspaces' => ['workspaces'],
        ]);

        return new ConfigDetailHydrator($policy);
    }

    private function ref(): SubjectRef
    {
        return new SubjectRef('data-hub-webhook-config', Uuid::v4(), 'car-changes-webhook');
    }
}
