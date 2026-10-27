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
use Doctrine\DBAL\Connection;
use function in_array;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use Pimcore\Bundle\ChangeControlBundle\ChangeSet\Hydrator\ReviewHydrator;
use Pimcore\Bundle\ChangeControlBundle\ChangeSet\Service\ChangeSetService;
use Pimcore\Bundle\ChangeControlBundle\Contract\ChangeControlAuthoringInterface;
use Pimcore\Bundle\ChangeControlBundle\Draft\BranchVisibility;
use Pimcore\Bundle\ChangeControlBundle\Hydrator\DetailHydratorRegistry;
use Pimcore\Bundle\ChangeControlBundle\Merge\Exception\UnsupportedMergeSaveModeException;
use Pimcore\Bundle\ChangeControlBundle\Merge\MergeSaveMode;
use Pimcore\Bundle\ChangeControlBundle\Storage\BranchMergeRepository;
use Pimcore\Bundle\ChangeControlBundle\Storage\BranchRepository;
use Pimcore\Bundle\ChangeControlBundle\Storage\Schema;
use Pimcore\Bundle\ChangeControlBundle\Storage\SubjectIdentityRepository;
use Pimcore\Bundle\ChangeControlBundle\Subject\Exception\SubjectAccessDeniedException;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectHandlerInterface;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectRef;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectRegistry;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Proposal\ChangeControl\ConfigStateShape;
use Pimcore\Bundle\DataHubBundle\Proposal\ChangeControl\ConfigSubjectHandler;
use Pimcore\Bundle\DataHubBundle\Proposal\StoredConfigurationsInterface;
use Pimcore\Bundle\DataHubBundle\Service\AdapterAvailabilityServiceInterface;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Pimcore\Bundle\DataHubBundle\Utils\Constants\PermissionConstants;
use Pimcore\Bundle\StudioBackendBundle\Security\Service\SecurityServiceInterface;
use Pimcore\Model\User;
use Pimcore\Model\UserInterface;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

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

    public function testAMergedStateTheAdapterRefusesIsNeverSaved(): void
    {
        $this->stored->put($this->configuration('orders', [
            'general' => ['active' => true, 'apiKey' => 'stored-key'],
        ]));
        $policy = new RecordingConfigPolicy(['Unknown event "x".']);

        try {
            $this->handler($policy)->applyMerged(
                $this->ref(),
                [
                    'general' => ['active' => true, 'apiKey' => 'proposed-key', 'modificationDate' => 3],
                    'eventsSchema' => ['events' => ['x']],
                ],
                $this->user([]),
                MergeSaveMode::Publish,
            );
            $this->fail('A reviewer\'s amendment must meet the adapter\'s checks too.');
        } catch (LogicException $e) {
            $this->assertSame(
                'Refusing to apply the webhook configuration "orders": Unknown event "x".',
                $e->getMessage(),
            );
        }

        $this->assertSame([], $policy->saved);
        $this->assertSame([[
            [
                'general' => ['active' => true, 'name' => 'orders', 'type' => 'webhooks'],
                'eventsSchema' => ['events' => ['x']],
            ],
            false,
        ]], $policy->checked);
    }

    public function testANewConfigurationTheAdapterRefusesIsNeverCreated(): void
    {
        $this->configurations->expects($this->never())->method('addConfiguration');
        $policy = new RecordingConfigPolicy(['Unknown class "y".']);

        try {
            $this->handler($policy)->applyMerged(
                $this->ref(),
                ['general' => ['active' => false]],
                $this->user([]),
                MergeSaveMode::Publish,
            );
            $this->fail('A reference that went away while the proposal waited must not be created.');
        } catch (LogicException $e) {
            $this->assertStringEndsWith('Unknown class "y".', $e->getMessage());
        }

        $this->assertTrue($policy->checked[0][1]);
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

    public function testAConfigurationThatCannotBeLookedUpIsNeitherReviewedNorApproved(): void
    {
        $stored = $this->createStub(StoredConfigurationsInterface::class);
        $stored->method('find')->willThrowException(new RuntimeException('unreadable entry'));
        $adapters = $this->createStub(AdapterAvailabilityServiceInterface::class);
        $adapters->method('isEnabled')->willReturn(true);
        $handler = new ConfigSubjectHandler(new RecordingConfigPolicy(), $this->configurations, $stored, $adapters);
        $creator = $this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG, PermissionConstants::PLUGIN_DATA_HUB_ADMIN]);

        foreach ([
            fn () => $handler->authorizeRead($this->ref(), $creator),
            fn () => $handler->authorize($this->ref(), $creator, MergeSaveMode::Publish),
        ] as $check) {
            try {
                $check();
                $this->fail('An unreadable configuration must not pass for a new one.');
            } catch (RuntimeException $e) {
                $this->assertSame('unreadable entry', $e->getMessage());
            }
        }
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

    /**
     * A reviewer's reject runs through Change Control's own discard, on an agent proposal for a
     * changed and for a new configuration: the change set closes, nothing reaches the adapter.
     */
    public function testRejectingAProposalWritesNothing(): void
    {
        $existing = $this->configuration('orders', ['general' => ['active' => true, 'description' => 'old']], allowed: [
            PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_READ,
            PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_UPDATE,
        ]);
        $this->stored->put($existing);
        $this->configurations->expects($this->never())->method('addConfiguration');
        $this->configurations->expects($this->never())->method('deleteConfiguration');
        $policy = new RecordingConfigPolicy();
        $handler = $this->handler($policy);
        $user = $this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG, PermissionConstants::PLUGIN_DATA_HUB_ADMIN]);
        $discarded = [];

        foreach (['orders', 'invoices'] as $name) {
            $subject = new SubjectRef('data-hub-webhook-config', Uuid::v4(), $name);
            $branch = Uuid::v4();
            $changeSets = $this->changeSets($handler, $subject, $branch, $user, $discarded);
            $changeSets->discard($branch->toRfc4122(), 'session-1');
            $this->assertSame([$branch->toRfc4122(), 7, 'session-1'], $discarded[$name] ?? null);
        }

        $this->assertSame([], $policy->saved);
        $this->assertNull($this->stored->find('invoices'));
        $this->assertSame($existing, $this->stored->find('orders'));
        $this->assertSame(['general' => ['active' => true, 'description' => 'old']], $handler->readState($this->ref()));
    }

    /**
     * Change Control's change set service over one open agent proposal on $subject.
     *
     * @param array<string, array{string, ?int, ?string}> $discarded by configuration name
     */
    private function changeSets(
        ConfigSubjectHandler $handler,
        SubjectRef $subject,
        Uuid $branch,
        User $user,
        array &$discarded,
    ): ChangeSetService {
        $authoring = $this->createStub(ChangeControlAuthoringInterface::class);
        $authoring->method('subjectsOf')->willReturn([$subject]);
        $authoring->method('discard')->willReturnCallback(
            static function (Uuid $closed, ?int $closedBy, ?string $contextRef) use (&$discarded, $subject): bool {
                $discarded[$subject->ref] = [$closed->toRfc4122(), $closedBy, $contextRef];

                return true;
            }
        );
        $db = $this->createStub(Connection::class);
        $db->method('fetchAssociative')->willReturn([
            'id' => 1,
            'uuid' => $branch->toBinary(),
            'kind' => 0,
            'state' => Schema::STATE_DRAFT,
            'name' => 'Webhook configuration',
            'subject_uuid' => $subject->uuid->toBinary(),
            's_type' => $subject->subjectType,
            's_ref' => $subject->ref,
            'created_via' => 'agent',
            'created_by' => 3,
            'context_ref' => 'session-1',
            'context' => null,
            'closed_at' => null,
        ]);
        $security = $this->createStub(SecurityServiceInterface::class);
        $security->method('getCurrentUser')->willReturn($user);

        return new ChangeSetService(
            $authoring,
            new BranchRepository($db),
            new BranchVisibility(),
            $this->unused(SubjectIdentityRepository::class),
            new SubjectRegistry(new ServiceLocator([$subject->subjectType => static fn () => $handler])),
            $this->unused(DetailHydratorRegistry::class),
            $this->unused(ReviewHydrator::class),
            $security,
            $this->createStub(EventDispatcherInterface::class),
            $this->unused(BranchMergeRepository::class),
        );
    }

    /**
     * A collaborator a discard never asks; built without its own wiring, which is not this test's.
     *
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    private function unused(string $class): object
    {
        return (new ReflectionClass($class))->newInstanceWithoutConstructor();
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
