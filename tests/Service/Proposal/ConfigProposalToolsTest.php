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
use function json_decode;
use Mcp\Schema\Content\TextContent;
use Mcp\Schema\Result\CallToolResult;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Proposal\Mcp\ConfigProposalTools;
use Pimcore\Bundle\DataHubBundle\Proposal\StoredConfigurationsInterface;
use Pimcore\Bundle\DataHubBundle\Service\AdapterAvailabilityServiceInterface;
use Pimcore\Bundle\DataHubBundle\Utils\Constants\PermissionConstants;
use Pimcore\Bundle\PimcoreAgentBundle\Proposal\ProposalResolverInterface;
use Pimcore\Bundle\PimcoreAgentBundle\Proposal\ProposalResolverRegistry;
use Pimcore\Bundle\PimcoreAgentBundle\Proposal\ProposalWidgetEmitter;
use Pimcore\Bundle\PimcoreAgentBundle\Security\BoundSessionReferenceResolver;
use Pimcore\Bundle\PimcoreAgentBundle\Service\AgentSessionServiceInterface;
use Pimcore\Bundle\PimcoreAgentBundle\Service\TaskContextResolverInterface;
use Pimcore\Bundle\StudioBackendBundle\Mcp\Exception\InvalidMcpToolArgumentException;
use Pimcore\Bundle\StudioBackendBundle\Mcp\Tool\McpToolErrorHandlerInterface;
use Pimcore\Bundle\StudioBackendBundle\Security\Authenticator\Mcp\McpAccessTokenAuthenticator;
use Pimcore\Bundle\StudioBackendBundle\Security\Service\SecurityServiceInterface;
use Pimcore\Model\User;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * The agent bundle is an optional peer, so these skip rather than fail where it is not installed.
 */
class ConfigProposalToolsTest extends Unit
{
    private const string TOOL = 'webhook_config_propose';

    private const string TOO_LONG = 'name is too long';

    private InMemoryStoredConfigurations $stored;

    /** @var list<array{string, string, array<string, mixed>, int}> */
    private array $recorded = [];

    private ?string $sessionId = 'session-1';

    private bool $adapterEnabled = true;

    protected function setUp(): void
    {
        if (!class_exists(ProposalWidgetEmitter::class) || !class_exists(CallToolResult::class)) {
            $this->markTestSkipped('The agent bundle or the MCP SDK is not installed.');
        }

        $this->stored = new InMemoryStoredConfigurations();
    }

    public function testWithoutTheDataHubConfigPermissionEveryToolIsDenied(): void
    {
        $tools = $this->tools($this->user([]));
        $policy = new RecordingConfigPolicy();

        foreach ([
            $tools->list($policy, self::TOOL),
            $tools->get($policy, self::TOOL, 'orders'),
            $tools->propose($policy, self::TOOL, 'orders', ['general' => ['active' => true]], null),
        ] as $result) {
            $this->assertSame(
                ['error' => 'Missing permission plugin_datahub_config', 'code' => 'permission_denied'],
                $this->payload($result, true),
            );
        }
        $this->assertSame([], $this->recorded);
    }

    public function testAFailingUserLookupIsADenial(): void
    {
        $security = $this->createStub(SecurityServiceInterface::class);
        $security->method('getCurrentUser')->willThrowException(new RuntimeException('no token'));

        $result = $this->tools(null, $security)->list(new RecordingConfigPolicy(), self::TOOL);

        $this->assertSame('permission_denied', $this->payload($result, true)['code']);
    }

    public function testTheListShowsOnlyThisAdaptersConfigurationsTheUserMayRead(): void
    {
        $this->stored->put($this->configuration('orders', [
            'general' => ['group' => 'shop', 'description' => 'Order hooks', 'active' => 1],
            'eventsSchema' => ['events' => ['pimcore.dataobject.postUpdate']],
        ]));
        $this->stored->put($this->configuration('hidden', ['general' => []], allowed: []));
        $this->stored->put($this->configuration('products', ['general' => []], 'graphql'));

        $result = $this->tools($this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]))
            ->list(new RecordingConfigPolicy(), self::TOOL);

        $this->assertSame(['configurations' => [[
            'name' => 'orders',
            'group' => 'shop',
            'description' => 'Order hooks',
            'active' => true,
            'events' => ['pimcore.dataobject.postUpdate'],
        ]]], $this->payload($result));
    }

    public function testAFailureWhileListingIsHandled(): void
    {
        $stored = $this->createStub(StoredConfigurationsInterface::class);
        $stored->method('all')->willThrowException(new RuntimeException('database gone'));
        $errors = $this->createMock(McpToolErrorHandlerInterface::class);
        $errors->expects($this->once())->method('handle')->willReturn('Something went wrong (ref 1).');

        $result = $this->tools($this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]), null, $errors, $stored)
            ->list(new RecordingConfigPolicy(), self::TOOL);

        $this->assertSame(
            ['error' => 'Something went wrong (ref 1).', 'code' => 'internal_error'],
            $this->payload($result, true),
        );
    }

    public function testAFailureWhileReadingIsHandled(): void
    {
        $stored = $this->createStub(StoredConfigurationsInterface::class);
        $stored->method('find')->willThrowException(new RuntimeException('database gone'));
        $errors = $this->createMock(McpToolErrorHandlerInterface::class);
        $errors->expects($this->once())->method('handle')
            ->with($this->isInstanceOf(RuntimeException::class), self::TOOL, ['name' => 'orders'])
            ->willReturn('Something went wrong (ref 2).');

        $result = $this->tools($this->admin(), null, $errors, $stored)
            ->get(new RecordingConfigPolicy(), self::TOOL, 'orders');

        $this->assertSame('internal_error', $this->payload($result, true)['code']);
    }

    public function testAnArgumentErrorWhileProposingIsReportedAsSuch(): void
    {
        $stored = $this->createStub(StoredConfigurationsInterface::class);
        $stored->method('find')->willThrowException(new InvalidMcpToolArgumentException(self::TOO_LONG));
        $errors = $this->createStub(McpToolErrorHandlerInterface::class);
        $errors->method('handle')->willReturn(self::TOO_LONG);

        $result = $this->tools($this->admin(), null, $errors, $stored)
            ->propose(new RecordingConfigPolicy(), self::TOOL, 'orders', ['general' => []], null);

        $this->assertSame(
            ['error' => self::TOO_LONG, 'code' => 'invalid_request'],
            $this->payload($result, true),
        );
    }

    public function testGetLeavesWithheldPathsAndBookkeepingOut(): void
    {
        $this->stored->put($this->configuration('orders', [
            'general' => ['active' => true, 'apiKey' => 'secret', 'modificationDate' => 3, 'writeable' => true],
            'security' => ['method' => 'header'],
            'eventsSchema' => ['events' => []],
        ]));

        $result = $this->tools($this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]))
            ->get(new RecordingConfigPolicy(), self::TOOL, 'orders');

        $this->assertSame([
            'name' => 'orders',
            'configuration' => ['general' => ['active' => true], 'eventsSchema' => ['events' => []]],
            'withheld' => ['security', 'general.apiKey'],
        ], $this->payload($result));
    }

    public function testAnotherAdaptersOrAnUnreadableConfigurationIsNotFound(): void
    {
        $this->stored->put($this->configuration('products', ['general' => []], 'graphql'));
        $this->stored->put($this->configuration('hidden', ['general' => []], allowed: []));
        $tools = $this->tools($this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]));

        foreach (['products', 'hidden', 'missing'] as $name) {
            $this->assertSame(
                'not_found',
                $this->payload($tools->get(new RecordingConfigPolicy(), self::TOOL, $name), true)['code'],
            );
        }
    }

    public function testAChangeIsRecordedAsTheStoredDocumentWithTheProposalFoldedIn(): void
    {
        $this->stored->put($this->configuration('orders', [
            'general' => ['active' => true, 'description' => 'old', 'apiKey' => 'secret', 'modificationDate' => 3],
            'eventsSchema' => ['events' => ['a']],
        ]));

        $result = $this->tools($this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]))->propose(
            new RecordingConfigPolicy(),
            self::TOOL,
            'orders',
            [
                'general' => ['description' => 'new', 'name' => 'renamed', 'createDate' => 1],
                'eventsSchema' => ['events' => ['b']],
            ],
            'Subscribe to b',
        );

        $payload = $this->payload($result);
        $this->assertSame('orders', $payload['name']);
        $this->assertSame('Subscribe to b', $payload['widget']['summary']);
        $this->assertSame('config-widget', $payload['widget']['widgetType']);
        $this->assertSame([$payload['proposalId']], $payload['widget']['proposalIds']);

        $this->assertCount(1, $this->recorded);
        [$sessionId, $proposalId, $data, $userId] = $this->recorded[0];
        $this->assertSame('session-1', $sessionId);
        $this->assertSame($payload['proposalId'], $proposalId);
        $this->assertSame(7, $userId);
        $this->assertSame([
            'proposalType' => 'subject-update',
            'subjectType' => 'data-hub-webhook-config',
            'subjectRef' => 'orders',
            'subjectState' => [
                'general' => ['active' => true, 'description' => 'new', 'name' => 'orders', 'type' => 'webhooks'],
                'eventsSchema' => ['events' => ['b']],
            ],
            'label' => 'Proposed changes to the orders webhook configuration',
            'isNew' => false,
        ], $data);
    }

    public function testANewConfigurationIsProposedSwitchedOff(): void
    {
        $result = $this->tools($this->user([
            PermissionConstants::PLUGIN_DATA_HUB_CONFIG,
            PermissionConstants::PLUGIN_DATA_HUB_ADAPTER_PREFIX . 'webhooks',
        ]))->propose(new RecordingConfigPolicy(), self::TOOL, 'orders', '{"general":{"description":"d"}}', null);

        $this->assertFalse($result->isError);
        $data = $this->recorded[0][2];
        $this->assertTrue($data['isNew']);
        $this->assertSame('A new orders webhook configuration', $data['label']);
        $this->assertSame(
            ['description' => 'd', 'name' => 'orders', 'type' => 'webhooks', 'active' => false, 'path' => ''],
            $data['subjectState']['general'],
        );
    }

    public function testANewConfigurationKeepsAnActiveFlagAndPathItWasProposedWith(): void
    {
        $yaml = "general:\n  active: true\n  path: shop\n";
        $this->tools($this->admin())->propose(new RecordingConfigPolicy(), self::TOOL, 'orders', $yaml, null);

        $general = $this->recorded[0][2]['subjectState']['general'];
        $this->assertTrue($general['active']);
        $this->assertSame('shop', $general['path']);
    }

    /**
     * @return iterable<string, array{array<string, mixed>|string, string}>
     */
    public static function refusedDocuments(): iterable
    {
        yield 'not an object' => ['just text', 'The configuration must be an object, or a JSON or YAML object string.'];
        yield 'broken YAML' => [
            "general: [\n",
            'The configuration must be an object, or a JSON or YAML object string.',
        ];
        yield 'unknown section' => [
            ['general' => [], 'schema' => []],
            'schema is not part of a webhook configuration. Read the current document with webhook_config_get',
        ];
        yield 'withheld section' => [
            ['security' => ['method' => 'none']],
            'security cannot be proposed. Credentials are set by a person.',
        ];
        yield 'withheld key' => [['general' => ['apiKey' => 'k']], 'general.apiKey cannot be proposed.'];
    }

    /**
     * @param array<string, mixed>|string $document
     *
     * @dataProvider refusedDocuments
     */
    public function testADocumentThatIsNotAWebhookConfigurationIsRefused(array|string $document, string $refusal): void
    {
        $this->stored->put($this->configuration('orders', ['general' => ['active' => true]]));

        $result = $this->tools($this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]))
            ->propose(new RecordingConfigPolicy(), self::TOOL, 'orders', $document, null);

        $this->assertStringStartsWith($refusal, $this->payload($result, true)['error']);
        $this->assertSame([], $this->recorded);
    }

    public function testTheAdaptersOwnProblemsAreAllReported(): void
    {
        $this->stored->put($this->configuration('orders', ['general' => ['active' => true]]));

        $result = $this->tools($this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]))->propose(
            new RecordingConfigPolicy(['Unknown event "x".', 'Unknown class "y".']),
            self::TOOL,
            'orders',
            ['general' => ['active' => false]],
            null,
        );

        $this->assertSame("Unknown event \"x\".\nUnknown class \"y\".", $this->payload($result, true)['error']);
        $this->assertSame([], $this->recorded);
    }

    public function testAProposalThatChangesNothingIsRefused(): void
    {
        $this->stored->put($this->configuration('orders', [
            'general' => ['active' => true, 'modificationDate' => 3],
            'eventsSchema' => ['events' => ['a']],
        ]));

        $result = $this->tools($this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]))->propose(
            new RecordingConfigPolicy(),
            self::TOOL,
            'orders',
            ['general' => ['active' => true, 'modificationDate' => 9], 'eventsSchema' => ['events' => ['a']]],
            null,
        );

        $this->assertStringStartsWith(
            'This leaves the webhook configuration "orders" exactly as it is',
            $this->payload($result, true)['error'],
        );
        $this->assertSame([], $this->recorded);
    }

    public function testWithoutAChatSessionNothingIsRecorded(): void
    {
        $this->sessionId = null;
        $this->stored->put($this->configuration('orders', ['general' => ['active' => true]]));

        $result = $this->tools($this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]))
            ->propose(new RecordingConfigPolicy(), self::TOOL, 'orders', ['general' => ['active' => false]], null);

        $this->assertSame('No chat session context.', $this->payload($result, true)['error']);
        $this->assertSame([], $this->recorded);
    }

    public function testAConfigurationTheUserMayNotReadCannotBeProposedOnto(): void
    {
        $this->stored->put($this->configuration('orders', ['general' => ['active' => true]], allowed: []));

        $result = $this->tools($this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]))
            ->propose(new RecordingConfigPolicy(), self::TOOL, 'orders', ['general' => ['active' => false]], null);

        $this->assertSame('No webhook configuration named "orders".', $this->payload($result, true)['error']);
    }

    public function testANewConfigurationNeedsAValidName(): void
    {
        $result = $this->tools($this->admin())
            ->propose(new RecordingConfigPolicy(), self::TOOL, '-orders', ['general' => []], null);

        $this->assertStringStartsWith('"-orders" cannot name a configuration', $this->payload($result, true)['error']);
    }

    public function testANameAnotherAdapterUsesCannotBeProposed(): void
    {
        $this->stored->put($this->configuration('products', ['general' => ['active' => true]], 'graphql'));

        $result = $this->tools($this->admin())
            ->propose(new RecordingConfigPolicy(), self::TOOL, 'products', ['general' => ['active' => false]], null);

        $this->assertSame(
            'The name "products" is taken by another Data Hub configuration: choose another name.',
            $this->payload($result, true)['error'],
        );
        $this->assertSame([], $this->recorded);
    }

    public function testANewConfigurationNeedsTheCreatePermission(): void
    {
        $result = $this->tools($this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]))
            ->propose(new RecordingConfigPolicy(), self::TOOL, 'orders', ['general' => []], null);

        $this->assertSame(
            'Missing permission to create a webhook configuration.',
            $this->payload($result, true)['error'],
        );
        $this->assertSame([], $this->recorded);
    }

    public function testADisabledAdaptersConfigurationsAreUnavailable(): void
    {
        $this->adapterEnabled = false;
        $this->stored->put($this->configuration('orders', ['general' => ['active' => true]]));
        $tools = $this->tools($this->admin());
        $policy = new RecordingConfigPolicy();

        foreach ([
            $tools->list($policy, self::TOOL),
            $tools->get($policy, self::TOOL, 'orders'),
            $tools->propose($policy, self::TOOL, 'orders', ['general' => ['active' => false]], null),
            $tools->propose($policy, self::TOOL, 'fresh', ['general' => []], null),
        ] as $result) {
            $this->assertSame(
                ['error' => 'The webhooks adapter is not enabled on this installation.', 'code' => 'not_found'],
                $this->payload($result, true),
            );
        }
        $this->assertSame([], $this->recorded);
    }

    private function tools(
        ?User $user,
        ?SecurityServiceInterface $security = null,
        ?McpToolErrorHandlerInterface $errors = null,
        ?StoredConfigurationsInterface $stored = null,
    ): ConfigProposalTools {
        if ($security === null) {
            $security = $this->createStub(SecurityServiceInterface::class);
            $security->method('getCurrentUser')->willReturn($user);
        }

        $sessions = $this->createStub(AgentSessionServiceInterface::class);
        $sessions->method('setProposalData')->willReturnCallback(
            function (string $sessionId, string $proposalId, array $data, int $userId): void {
                $this->recorded[] = [$sessionId, $proposalId, $data, $userId];
            }
        );

        $resolver = $this->createStub(ProposalResolverInterface::class);
        $resolver->method('getType')->willReturn('subject-update');
        $resolver->method('getWidgetType')->willReturn('config-widget');

        $requests = new RequestStack();
        $request = new Request();
        if ($this->sessionId !== null) {
            $request->attributes->set(McpAccessTokenAuthenticator::REQUEST_ATTR_REFERENCE, $this->sessionId);
        }
        $requests->push($request);

        $adapters = $this->createStub(AdapterAvailabilityServiceInterface::class);
        $adapters->method('isEnabled')->willReturnCallback(fn (string $type): bool => $this->adapterEnabled);

        return new ConfigProposalTools(
            $sessions,
            new ProposalWidgetEmitter(
                new ProposalResolverRegistry([$resolver]),
                $this->createStub(TaskContextResolverInterface::class),
            ),
            new BoundSessionReferenceResolver($requests),
            $security,
            $errors ?? $this->createStub(McpToolErrorHandlerInterface::class),
            $stored ?? $this->stored,
            $adapters,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(CallToolResult $result, bool $isError = false): array
    {
        $this->assertSame($isError, $result->isError);
        $content = $result->content[0];
        $this->assertInstanceOf(TextContent::class, $content);

        return json_decode($content->text, true);
    }

    /**
     * @param array<string, mixed> $document
     * @param list<string> $allowed what the configuration's own permissions grant
     */
    private function configuration(
        string $name,
        array $document,
        string $type = 'webhooks',
        array $allowed = ['read'],
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

    private function admin(): User
    {
        return $this->user([PermissionConstants::PLUGIN_DATA_HUB_CONFIG, PermissionConstants::PLUGIN_DATA_HUB_ADMIN]);
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
}
