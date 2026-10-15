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
use function in_array;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use Pimcore\Bundle\ChangeControlBundle\Merge\Exception\UnsupportedMergeSaveModeException;
use Pimcore\Bundle\ChangeControlBundle\Merge\MergeSaveMode;
use Pimcore\Bundle\ChangeControlBundle\Subject\Exception\SubjectAccessDeniedException;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectHandlerInterface;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectRef;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Proposal\ChangeControl\ConfigStateShape;
use Pimcore\Bundle\DataHubBundle\Proposal\ChangeControl\ConfigSubjectHandler;
use Pimcore\Bundle\DataHubBundle\Service\AdapterAvailabilityServiceInterface;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Pimcore\Bundle\DataHubBundle\Utils\Constants\PermissionConstants;
use Pimcore\Model\User;
use Pimcore\Model\UserInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Change Control is an optional peer, so these skip rather than fail where it is not installed.
 */
class ConfigSubjectHandlerTest extends Unit
{
    private const string PATH = 'shop/exports';

    private InMemoryStoredConfigurations $stored;

    private ConfigurationServiceInterface&MockObject $configurations;

    private bool $adapterEnabled = true;

    protected function setUp(): void
    {
        if (!interface_exists(SubjectHandlerInterface::class)) {
            $this->markTestSkipped('Change Control is not installed.');
        }

        $this->stored = new InMemoryStoredConfigurations();
        $this->configurations = $this->createMock(ConfigurationServiceInterface::class);
    }

    public function testApplyingWritesTheMergedConfigurationWithItsWithheldPathsFromStorage(): void
    {
        $this->stored->put($this->configuration('orders', [
            'general' => ['active' => true, 'description' => 'old', 'apiKey' => 'stored-key'],
            'security' => ['method' => 'header'],
        ]));
        $policy = new RecordingConfigPolicy();

        $this->handler($policy)->applyMerged($this->ref(), [
            'general' => ['active' => true, 'description' => 'new', 'apiKey' => 'proposed-key'],
            'security' => ['method' => 'none'],
            'eventsSchema' => ['events' => ['pimcore.dataobject.postUpdate']],
        ], $this->user([]), MergeSaveMode::Publish);

        $this->assertSame([['orders', [
            'general' => [
                'active' => true,
                'description' => 'new',
                'apiKey' => 'stored-key',
                'name' => 'orders',
                'type' => 'webhooks',
            ],
            'eventsSchema' => ['events' => ['pimcore.dataobject.postUpdate']],
            'security' => ['method' => 'header'],
        ]]], $policy->saved);
    }

    public function testTheSubjectsNameAndTypeWinOverTheMergedState(): void
    {
        $this->stored->put($this->configuration('orders', ['general' => ['active' => false]]));
        $policy = new RecordingConfigPolicy();

        $this->handler($policy)->applyMerged(
            $this->ref(),
            ['general' => ['name' => 'renamed', 'type' => 'graphql', 'active' => false]],
            $this->user([]),
            MergeSaveMode::Publish,
        );

        $this->assertSame('orders', $policy->saved[0][1]['general']['name']);
        $this->assertSame('webhooks', $policy->saved[0][1]['general']['type']);
    }

    public function testANewConfigurationIsCreatedUnderTheAdaptersTypeBeforeItIsSaved(): void
    {
        $this->configurations->expects($this->once())->method('addConfiguration')
            ->with('orders', 'webhooks', self::PATH)
            ->willReturnCallback(function (string $name): string {
                $this->stored->put($this->configuration($name, ['general' => []]));

                return $name;
            });
        $policy = new RecordingConfigPolicy();

        $this->handler($policy)->applyMerged(
            $this->ref(),
            ['general' => ['active' => false, 'path' => self::PATH]],
            $this->user([]),
            MergeSaveMode::Publish,
        );

        $this->assertSame(
            ['active' => false, 'path' => self::PATH, 'name' => 'orders', 'type' => 'webhooks'],
            $policy->saved[0][1]['general'],
        );
    }

    public function testACreateWhoseSaveFailsLeavesNoConfigurationBehind(): void
    {
        $this->configurations->method('addConfiguration')->willReturnCallback(function (string $name): string {
            $this->stored->put($this->configuration($name, ['general' => []]));

            return $name;
        });
        $this->configurations->expects($this->once())->method('deleteConfiguration')
            ->with('orders')
            ->willReturnCallback(fn (string $name) => $this->stored->remove($name));

        try {
            $this->handler(new RecordingConfigPolicy(saveFails: true))->applyMerged(
                $this->ref(),
                ['general' => ['active' => false]],
                $this->user([]),
                MergeSaveMode::Publish,
            );
            $this->fail('The failed save must reach the approver.');
        } catch (RejectedConfigurationException $e) {
            $this->assertSame('The adapter rejected the configuration.', $e->getMessage());
        }

        $this->assertNull($this->stored->find('orders'));
    }

    public function testTheSaveFailureIsReportedEvenWhenTheCleanupFails(): void
    {
        $this->configurations->method('addConfiguration')->willReturnCallback(function (string $name): string {
            $this->stored->put($this->configuration($name, ['general' => []]));

            return $name;
        });
        $this->configurations->method('deleteConfiguration')->willThrowException(new LogicException('locked'));

        $this->expectExceptionMessage('The adapter rejected the configuration.');

        $this->handler(new RecordingConfigPolicy(saveFails: true))->applyMerged(
            $this->ref(),
            ['general' => ['active' => false]],
            $this->user([]),
            MergeSaveMode::Publish,
        );
    }

    public function testAnUpdateWhoseSaveFailsKeepsTheStoredConfiguration(): void
    {
        $this->stored->put($this->configuration('orders', ['general' => ['active' => true]]));
        $this->configurations->expects($this->never())->method('deleteConfiguration');

        $this->expectException(RejectedConfigurationException::class);

        $this->handler(new RecordingConfigPolicy(saveFails: true))->applyMerged(
            $this->ref(),
            ['general' => ['active' => false]],
            $this->user([]),
            MergeSaveMode::Publish,
        );
    }

    public function testANameAnotherAdapterOwnsIsNeverOverwritten(): void
    {
        $this->stored->put($this->configuration('orders', ['general' => ['active' => true]], 'graphql'));
        $this->configurations->expects($this->never())->method('addConfiguration');
        $policy = new RecordingConfigPolicy();

        try {
            $this->handler($policy)->applyMerged(
                $this->ref(),
                ['general' => ['active' => false]],
                $this->user([]),
                MergeSaveMode::Publish,
            );
            $this->fail('A graphql configuration must not be overwritten by a webhook proposal.');
        } catch (LogicException $e) {
            $this->assertStringContainsString('belongs to a graphql configuration', $e->getMessage());
        }

        $this->assertSame([], $policy->saved);
    }

    public function testAnEmptyStateIsNeverApplied(): void
    {
        $this->stored->put($this->configuration('orders', ['general' => ['active' => true]]));
        $policy = new RecordingConfigPolicy();

        try {
            $this->handler($policy)->applyMerged($this->ref(), [], $this->user([]), MergeSaveMode::Publish);
            $this->fail('An empty state would remove the configuration.');
        } catch (LogicException $e) {
            $this->assertStringStartsWith('Refusing to apply an empty state', $e->getMessage());
        }

        $this->assertCount(0, $policy->saved);
    }

    public function testOnlyAPublishIsApplied(): void
    {
        $this->expectException(UnsupportedMergeSaveModeException::class);

        $this->handler(new RecordingConfigPolicy())->applyMerged(
            $this->ref(),
            ['general' => ['active' => true]],
            $this->user([]),
            MergeSaveMode::Draft,
        );
    }

    public function testTheStateIsTheReadableDocumentOfThisAdaptersConfiguration(): void
    {
        $this->stored->put($this->configuration('orders', [
            'general' => ['active' => true, 'apiKey' => 'secret', 'modificationDate' => 1, 'writeable' => true],
            'security' => ['method' => 'header'],
            'eventsSchema' => ['events' => []],
        ]));

        $this->assertSame(
            ['general' => ['active' => true], 'eventsSchema' => ['events' => []]],
            $this->handler(new RecordingConfigPolicy())->readState($this->ref()),
        );
    }

    public function testAMissingConfigurationHasAnEmptyState(): void
    {
        $this->assertSame([], $this->handler(new RecordingConfigPolicy())->readState($this->ref()));
    }

    public function testAnotherAdaptersConfigurationIsNeverShownAsTheBase(): void
    {
        $this->stored->put($this->configuration('orders', ['general' => ['active' => true]], 'graphql'));

        $this->assertSame([], $this->handler(new RecordingConfigPolicy())->readState($this->ref()));
    }

    public function testAConfigurationMergesByTheConfigurationShape(): void
    {
        $this->assertInstanceOf(
            ConfigStateShape::class,
            $this->handler(new RecordingConfigPolicy())->shape($this->ref()),
        );
    }

    public function testWithheldSectionsAreOutOfDraftScope(): void
    {
        $this->assertSame(['security'], $this->handler(new RecordingConfigPolicy())->outOfDraftScope());
    }

    public function testApprovingAChangeNeedsTheConfigurationsUpdatePermission(): void
    {
        $this->stored->put($this->configuration('orders', ['general' => []], allowed: ['read']));
        $handler = $this->handler(new RecordingConfigPolicy());
        $user = $this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]);

        $handler->authorizeRead($this->ref(), $user);

        $this->expectException(SubjectAccessDeniedException::class);
        $handler->authorize($this->ref(), $user, MergeSaveMode::Publish);
    }

    public function testAUserWhoMayUpdateTheConfigurationMayApprove(): void
    {
        $this->stored->put($this->configuration('orders', ['general' => []], allowed: ['read', 'update']));

        $this->handler(new RecordingConfigPolicy())->authorize(
            $this->ref(),
            $this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]),
            MergeSaveMode::Publish,
        );
        $this->addToAssertionCount(1);
    }

    public function testWithoutTheDataHubConfigPermissionNothingIsAllowed(): void
    {
        $this->stored->put($this->configuration('orders', ['general' => []], allowed: ['read', 'update']));

        $this->expectException(SubjectAccessDeniedException::class);
        $this->handler(new RecordingConfigPolicy())->authorizeRead($this->ref(), $this->user([]));
    }

    public function testAnActorThatIsNoUserModelIsDenied(): void
    {
        $this->expectException(SubjectAccessDeniedException::class);
        $this->handler(new RecordingConfigPolicy())->authorizeRead(
            $this->ref(),
            $this->createStub(UserInterface::class),
        );
    }

    public function testOnlyAPublishIsAuthorized(): void
    {
        $this->stored->put($this->configuration('orders', ['general' => []], allowed: ['read', 'update']));

        $this->expectException(SubjectAccessDeniedException::class);
        $this->handler(new RecordingConfigPolicy())->authorize(
            $this->ref(),
            $this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]),
            MergeSaveMode::Save,
        );
    }

    public function testReviewingANewConfigurationNeedsOnlyTheAreaPermission(): void
    {
        $this->handler(new RecordingConfigPolicy())->authorizeRead(
            $this->ref(),
            $this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]),
        );
        $this->addToAssertionCount(1);
    }

    public function testApprovingANewConfigurationNeedsTheCreatePermission(): void
    {
        $this->expectException(SubjectAccessDeniedException::class);
        $this->handler(new RecordingConfigPolicy())->authorize(
            $this->ref(),
            $this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]),
            MergeSaveMode::Publish,
        );
    }

    /**
     * @return iterable<string, array{list<string>}>
     */
    public static function creators(): iterable
    {
        yield 'adapter permission' => [[PermissionConstants::PLUGIN_DATA_HUB_ADAPTER_PREFIX . 'webhooks']];
        yield 'Data Hub admin' => [[PermissionConstants::PLUGIN_DATA_HUB_ADMIN]];
    }

    /**
     * @param list<string> $permissions
     *
     * @dataProvider creators
     */
    public function testAUserWhoMayCreateMayApproveANewConfiguration(array $permissions): void
    {
        $this->handler(new RecordingConfigPolicy())->authorize(
            $this->ref(),
            $this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG, ...$permissions]),
            MergeSaveMode::Publish,
        );
        $this->addToAssertionCount(1);
    }

    public function testAConfigurationTheUserMayNotOpenCannotBeReviewed(): void
    {
        $this->stored->put($this->configuration('orders', ['general' => []]));

        $this->expectException(SubjectAccessDeniedException::class);
        $this->handler(new RecordingConfigPolicy())->authorizeRead(
            $this->ref(),
            $this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]),
        );
    }

    public function testADisabledAdaptersConfigurationCannotBeReviewedOrApproved(): void
    {
        $this->adapterEnabled = false;
        $this->stored->put($this->configuration('orders', ['general' => []], allowed: ['read', 'update']));
        $handler = $this->handler(new RecordingConfigPolicy());
        $user = $this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG, PermissionConstants::PLUGIN_DATA_HUB_ADMIN]);

        foreach ([
            fn () => $handler->authorizeRead($this->ref(), $user),
            fn () => $handler->authorize($this->ref(), $user, MergeSaveMode::Publish),
        ] as $probe) {
            try {
                $probe();
                $this->fail('A disabled adapter must not be reviewable.');
            } catch (SubjectAccessDeniedException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function testANewConfigurationOfADisabledAdapterCannotBeApproved(): void
    {
        $this->adapterEnabled = false;

        $this->expectException(SubjectAccessDeniedException::class);
        $this->handler(new RecordingConfigPolicy())->authorize(
            $this->ref(),
            $this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG, PermissionConstants::PLUGIN_DATA_HUB_ADMIN]),
            MergeSaveMode::Publish,
        );
    }

    private function handler(RecordingConfigPolicy $policy): ConfigSubjectHandler
    {
        $adapters = $this->createStub(AdapterAvailabilityServiceInterface::class);
        $adapters->method('isEnabled')->willReturnCallback(
            fn (string $type): bool => $this->adapterEnabled && $type === 'webhooks'
        );

        return new ConfigSubjectHandler($policy, $this->configurations, $this->stored, $adapters);
    }

    /**
     * @param array<string, mixed> $document
     * @param list<string> $allowed what the configuration's own permissions grant
     */
    private function configuration(
        string $name,
        array $document,
        string $type = 'webhooks',
        array $allowed = [],
    ): Configuration {
        $configuration = $this->createStub(Configuration::class);
        $configuration->method('getName')->willReturn($name);
        $configuration->method('getType')->willReturn($type);
        $configuration->method('getConfiguration')->willReturn($document);
        $configuration->method('isAllowed')->willReturnCallback(
            static fn (string $permission): bool => in_array($permission, $allowed, true)
        );

        return $configuration;
    }

    /**
     * @param list<string> $permissions
     */
    private function user(array $permissions): User
    {
        $user = new User();
        $user->setId(7);
        $user->setPermissions($permissions);

        return $user;
    }

    private function ref(): SubjectRef
    {
        return new SubjectRef('data-hub-webhook-config', Uuid::v4(), 'orders');
    }
}
