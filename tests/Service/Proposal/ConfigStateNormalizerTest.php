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

use function array_column;
use Codeception\Test\Unit;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use Pimcore\Bundle\ChangeControlBundle\Merge\EditableDataMerge;
use Pimcore\Bundle\ChangeControlBundle\Merge\MergeSaveMode;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectHandlerInterface;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectRef;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Proposal\ChangeControl\ConfigSubjectHandler;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigDocument;
use Pimcore\Bundle\DataHubBundle\Service\AdapterAvailabilityServiceInterface;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Pimcore\Model\User;
use Symfony\Component\Uid\Uuid;

/**
 * A policy that normalises the compared state: reviews and approvals of ConfigSubjectHandler through
 * Change Control's own merge. Change Control is an optional peer, so these skip where it is not installed.
 */
class ConfigStateNormalizerTest extends Unit
{
    private const string SHOP = '/shop';

    private const string BLOG = '/blog';

    private const string NEWS = '/news';

    private const string ROWS = 'workspaces.object';

    private InMemoryStoredConfigurations $stored;

    private ConfigurationServiceInterface&MockObject $configurations;

    protected function setUp(): void
    {
        if (!interface_exists(SubjectHandlerInterface::class)) {
            $this->markTestSkipped('Change Control is not installed.');
        }

        $this->stored = new InMemoryStoredConfigurations();
        $this->configurations = $this->createMock(ConfigurationServiceInterface::class);
    }

    public function testAnEditorSaveThePolicyNormalizesAwayIsNoConflict(): void
    {
        $review = $this->reviewAfterAnEditorSave(
            new RowIdNormalizingConfigPolicy(),
            [$this->row(self::SHOP, true, 'r1'), $this->row(self::BLOG, true, 'r2')],
        );

        $this->assertSame([], $review['conflicts']);
    }

    public function testARealChangeStillConflictsWhenThePolicyNormalizes(): void
    {
        $review = $this->reviewAfterAnEditorSave(
            new RowIdNormalizingConfigPolicy(),
            [$this->row(self::SHOP, false, 'r1'), $this->row(self::BLOG, true, 'r2')],
        );

        $this->assertSame([self::ROWS], array_column($review['conflicts'], 'path'));
    }

    public function testWithoutTheNormalizerAnEditorSaveStillConflicts(): void
    {
        $review = $this->reviewAfterAnEditorSave(
            new RecordingConfigPolicy(),
            [$this->row(self::SHOP, true, 'r1'), $this->row(self::BLOG, true, 'r2')],
        );

        $this->assertSame([self::ROWS], array_column($review['conflicts'], 'path'));
    }

    public function testTheEditorsEditAndTheProposalAreBothSavedOnApprove(): void
    {
        $policy = new RowIdNormalizingConfigPolicy();
        $review = $this->reviewAfterAnEditorSave(
            $policy,
            [$this->row(self::SHOP, true, 'r1'), $this->row(self::BLOG, true, 'r2')],
        );

        $review['handler']->applyMerged($this->ref(), $review['merged'], $this->user(), MergeSaveMode::Publish);

        $saved = $policy->saved[0][1];
        $this->assertSame('new', $saved['general']['description']);
        $this->assertSame(
            [$this->row(self::SHOP, true, 'r1'), $this->row(self::BLOG, true, 'r2'), $this->row(self::NEWS, true)],
            $saved['workspaces']['object'],
        );
    }

    public function testApprovingPutsBackWhatTheNormalizerDropped(): void
    {
        $this->stored->put($this->configuration([
            'general' => ['active' => true],
            'workspaces' => ['object' => [$this->row(self::SHOP, true, 'r1'), $this->row(self::BLOG, true, 'r2')]],
        ]));
        $policy = new RowIdNormalizingConfigPolicy();
        $handler = $this->handler($policy);

        $this->assertSame(
            ['general' => ['active' => true], 'workspaces' => ['object' => [
                $this->row(self::SHOP, true),
                $this->row(self::BLOG, true),
            ]]],
            $handler->readState($this->ref()),
        );

        $handler->applyMerged($this->ref(), [
            'general' => ['active' => true],
            'workspaces' => ['object' => [$this->row(self::BLOG, false), $this->row(self::NEWS, true)]],
        ], $this->user(), MergeSaveMode::Publish);

        $this->assertSame(
            [$this->row(self::BLOG, false, 'r2'), $this->row(self::NEWS, true)],
            $policy->saved[0][1]['workspaces']['object'],
        );
        // the adapter's checks see the state as the review compared it
        $this->assertSame(
            [$this->row(self::BLOG, false), $this->row(self::NEWS, true)],
            $policy->checked[0][0]['workspaces']['object'],
        );
    }

    public function testANewConfigurationIsRestoredFromNothing(): void
    {
        // the configuration a create makes may already carry values of its own
        $this->configurations->expects($this->once())->method('addConfiguration')
            ->willReturnCallback(function (string $name): string {
                $this->stored->put($this->configuration(['workspaces' => ['object' => [
                    $this->row(self::SHOP, true, 'minted'),
                ]]], $name));

                return $name;
            });
        $policy = new RowIdNormalizingConfigPolicy();

        $this->handler($policy)->applyMerged(
            $this->ref(),
            [
                ConfigDocument::CREATE_MARKER => true,
                'general' => ['active' => false],
                'workspaces' => ['object' => [$this->row(self::SHOP, true)]],
            ],
            $this->user(),
            MergeSaveMode::Publish,
        );

        $this->assertSame([[]], $policy->restoredFrom);
        $this->assertSame([$this->row(self::SHOP, true)], $policy->saved[0][1]['workspaces']['object']);
    }

    public function testACreateWhoseRestoreFailsLeavesTheNameFree(): void
    {
        $this->configurations->method('addConfiguration')->willReturnCallback(function (string $name): string {
            $this->stored->put($this->configuration(['general' => []], $name));

            return $name;
        });
        $this->configurations->expects($this->once())->method('deleteConfiguration')
            ->with('orders')
            ->willReturnCallback(fn (string $name) => $this->stored->remove($name));
        $policy = new RowIdNormalizingConfigPolicy(restoreFails: true);

        try {
            $this->handler($policy)->applyMerged(
                $this->ref(),
                [ConfigDocument::CREATE_MARKER => true, 'general' => ['active' => false]],
                $this->user(),
                MergeSaveMode::Publish,
            );
            $this->fail('The failed restore must reach the approver.');
        } catch (LogicException $e) {
            $this->assertSame('The stored ids could not be put back.', $e->getMessage());
        }

        $this->assertNull($this->stored->find('orders'));
        $this->assertSame([], $policy->saved);
    }

    /**
     * A proposal adds a workspace row; before it is approved, the editor saves the configuration
     * with a new description and the rows as given. Merged as Change Control's merge service does.
     *
     * @param list<array<string, mixed>> $rowsSaved
     *
     * @return array{
     *     handler: ConfigSubjectHandler,
     *     conflicts: list<array{path: string, base: mixed, main: mixed, ours: mixed}>,
     *     merged: array<string, mixed>
     * }
     */
    private function reviewAfterAnEditorSave(RecordingConfigPolicy $policy, array $rowsSaved): array
    {
        $this->stored->put($this->configuration([
            'general' => ['active' => true, 'description' => 'old'],
            'workspaces' => ['object' => [$this->row(self::SHOP, true), $this->row(self::BLOG, true)]],
        ]));
        $handler = $this->handler($policy);
        $base = $handler->readState($this->ref());
        $ours = $base;
        $ours['workspaces']['object'][] = $this->row(self::NEWS, true);

        $this->stored->put($this->configuration([
            'general' => ['active' => true, 'description' => 'new'],
            'workspaces' => ['object' => $rowsSaved],
        ]));
        $main = $handler->readState($this->ref());

        $merge = new EditableDataMerge($handler->shape($this->ref()));
        $delta = $merge->deltaOf($base, $ours);

        return [
            'handler' => $handler,
            'conflicts' => $merge->conflictsDeep($base, $main, $delta),
            'merged' => $merge->deepOverlay($main, $delta),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function row(string $path, bool $read, ?string $rowId = null): array
    {
        return $rowId === null
            ? ['cpath' => $path, 'read' => $read]
            : ['cpath' => $path, 'read' => $read, 'rowId' => $rowId];
    }

    private function handler(RecordingConfigPolicy $policy): ConfigSubjectHandler
    {
        $adapters = $this->createStub(AdapterAvailabilityServiceInterface::class);
        $adapters->method('isEnabled')->willReturn(true);

        return new ConfigSubjectHandler($policy, $this->configurations, $this->stored, $adapters);
    }

    /**
     * @param array<string, mixed> $document
     */
    private function configuration(array $document, string $name = 'orders'): Configuration
    {
        $configuration = $this->createStub(Configuration::class);
        $configuration->method('getName')->willReturn($name);
        $configuration->method('getType')->willReturn('webhooks');
        $configuration->method('getConfiguration')->willReturn($document);

        return $configuration;
    }

    private function user(): User
    {
        $user = new User();
        $user->setId(7);

        return $user;
    }

    private function ref(): SubjectRef
    {
        return new SubjectRef('data-hub-webhook-config', Uuid::v4(), 'orders');
    }
}
