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
use function json_decode;
use Mcp\Schema\Content\TextContent;
use Mcp\Schema\Result\CallToolResult;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigProposalPolicyInterface;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\GraphQlConfigPolicy;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\Tool\GetGraphQlConfigTool;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\Tool\ListGraphQlConfigsTool;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\Tool\ProposeGraphQlConfigTool;
use Pimcore\Bundle\DataHubBundle\Proposal\Mcp\ConfigProposalTools;
use Pimcore\Bundle\DataHubBundle\Proposal\PerEntityMergeInterface;
use Pimcore\Bundle\DataHubBundle\Service\AdapterAvailabilityServiceInterface;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Pimcore\Bundle\DataHubBundle\Utils\Constants\PermissionConstants;
use Pimcore\Bundle\PimcoreAgentBundle\Proposal\ProposalResolverInterface;
use Pimcore\Bundle\PimcoreAgentBundle\Proposal\ProposalResolverRegistry;
use Pimcore\Bundle\PimcoreAgentBundle\Proposal\ProposalWidgetEmitter;
use Pimcore\Bundle\PimcoreAgentBundle\Security\BoundSessionReferenceResolver;
use Pimcore\Bundle\PimcoreAgentBundle\Service\AgentSessionServiceInterface;
use Pimcore\Bundle\PimcoreAgentBundle\Service\TaskContextResolverInterface;
use Pimcore\Bundle\StudioBackendBundle\Mcp\Tool\McpToolErrorHandlerInterface;
use Pimcore\Bundle\StudioBackendBundle\Security\Authenticator\Mcp\McpAccessTokenAuthenticator;
use Pimcore\Bundle\StudioBackendBundle\Security\Service\SecurityServiceInterface;
use Pimcore\Model\User;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * The GraphQL tools hand their own name and the GraphQL policy to the shared proposal tools.
 * The agent bundle is an optional peer, so these skip rather than fail where it is not installed.
 */
class GraphQlConfigToolsTest extends Unit
{
    private const string NAME = 'shop';

    private InMemoryStoredConfigurations $stored;

    /** @var list<array<string, mixed>> */
    private array $recorded = [];

    protected function setUp(): void
    {
        if (!class_exists(ProposalWidgetEmitter::class) || !class_exists(CallToolResult::class)) {
            $this->markTestSkipped('The agent bundle or the MCP SDK is not installed.');
        }

        $this->stored = new InMemoryStoredConfigurations();
        $this->stored->put($this->configuration(self::NAME, 'graphql', [
            'general' => ['group' => 'web', 'description' => 'old', 'active' => true],
            'schema' => ['queryEntities' => ['Car' => ['id' => 'Car']], 'mutationEntities' => []],
            'security' => ['method' => 'datahub_apikey', 'apikey' => ['secret']],
        ]));
        $this->stored->put($this->configuration('orders', 'webhooks', ['general' => ['active' => true]]));
    }

    public function testTheListToolListsOnlyGraphQlConfigurationsWithTheirSchemaSummary(): void
    {
        $result = (new ListGraphQlConfigsTool($this->tools(), $this->policy()))->execute();

        $this->assertSame(['configurations' => [[
            'name' => self::NAME,
            'group' => 'web',
            'description' => 'old',
            'active' => true,
            'queryEntities' => ['Car'],
            'mutationEntityCount' => 0,
        ]]], $this->payload($result));
    }

    public function testTheGetToolWithholdsTheSecuritySettings(): void
    {
        $result = (new GetGraphQlConfigTool($this->tools(), $this->policy()))->execute(self::NAME);

        $this->assertSame([
            'name' => self::NAME,
            'configuration' => [
                'general' => ['group' => 'web', 'description' => 'old', 'active' => true],
                'schema' => ['queryEntities' => ['Car' => ['id' => 'Car']], 'mutationEntities' => []],
            ],
            'withheld' => GraphQlConfigPolicy::WITHHELD,
        ], $this->payload($result));
    }

    public function testTheGetToolDoesNotFindAnotherAdaptersConfiguration(): void
    {
        $result = (new GetGraphQlConfigTool($this->tools(), $this->policy()))->execute('orders');

        $this->assertSame(
            ['error' => 'No GraphQL configuration named "orders".', 'code' => 'not_found'],
            $this->payload($result, true),
        );
    }

    public function testTheProposeToolRecordsAGraphQlSubjectUpdate(): void
    {
        // no query entities: the policy looks a class up by name in the database
        $this->stored->put($this->configuration(self::NAME, 'graphql', [
            'general' => ['group' => 'web', 'description' => 'old', 'active' => true],
            'security' => ['method' => 'datahub_apikey', 'apikey' => ['secret']],
        ]));

        $result = (new ProposeGraphQlConfigTool($this->tools(), $this->policy()))
            ->execute(self::NAME, '{"general":{"description":"new"}}', 'Describe the shop endpoint');

        $payload = $this->payload($result);
        $this->assertSame(self::NAME, $payload['name']);
        $this->assertSame('Describe the shop endpoint', $payload['widget']['summary']);
        $this->assertCount(1, $this->recorded);
        $this->assertSame(GraphQlConfigPolicy::SUBJECT_TYPE, $this->recorded[0]['subjectType']);
        $this->assertSame(self::NAME, $this->recorded[0]['subjectRef']);
        $this->assertSame('Proposed changes to the shop GraphQL configuration', $this->recorded[0]['label']);
        $this->assertSame(
            ['group' => 'web', 'description' => 'new', 'active' => true, 'name' => self::NAME, 'type' => 'graphql'],
            $this->recorded[0]['subjectState']['general'],
        );
        $this->assertArrayNotHasKey('security', $this->recorded[0]['subjectState']);
    }

    public function testAProposalCarryingOneEntityIsRecordedWithEveryOtherEntityKept(): void
    {
        $event = ['id' => 'Event', 'columnConfig' => ['columns' => [['name' => 'title'], ['name' => 'date']]]];
        $this->stored->put($this->configuration(self::NAME, 'graphql', [
            'general' => ['active' => true],
            'schema' => [
                'queryEntities' => [
                    'Event' => $event,
                    'Car' => ['id' => 'Car', 'columnConfig' => ['columns' => [['name' => 'name']]]],
                    'Manufacturer' => ['id' => 'Manufacturer'],
                ],
                'mutationEntities' => [],
            ],
            'security' => ['method' => 'datahub_apikey', 'apikey' => ['secret']],
        ]));
        $car = ['id' => 'Car', 'columnConfig' => ['columns' => [['name' => 'name'], ['name' => 'color']]]];

        // the GraphQL policy looks classes up in the database, so its entity paths ride a stand-in
        $result = $this->tools()->propose(
            $this->entityPolicy(),
            'propose_graphql_config',
            self::NAME,
            ['schema' => ['queryEntities' => ['Car' => $car, 'Manufacturer' => null]]],
            null,
        );

        $this->assertSame(self::NAME, $this->payload($result)['name']);
        $this->assertSame(
            ['queryEntities' => ['Event' => $event, 'Car' => $car], 'mutationEntities' => []],
            $this->recorded[0]['subjectState']['schema'],
        );
    }

    public function testTheProposeToolRefusesTheSecuritySettings(): void
    {
        $result = (new ProposeGraphQlConfigTool($this->tools(), $this->policy()))
            ->execute(self::NAME, ['security' => ['method' => 'none']]);

        $this->assertStringStartsWith(
            'security cannot be proposed. The security settings hold the API keys',
            $this->payload($result, true)['error'],
        );
        $this->assertSame([], $this->recorded);
    }

    private function policy(): GraphQlConfigPolicy
    {
        return new GraphQlConfigPolicy(
            $this->createStub(ConfigurationServiceInterface::class),
            new EventDispatcher(),
        );
    }

    /**
     * GraphQL's sections and entity paths, without its database lookups.
     */
    private function entityPolicy(): ConfigProposalPolicyInterface&PerEntityMergeInterface
    {
        return new class($this->policy()) implements ConfigProposalPolicyInterface, PerEntityMergeInterface {
            public function __construct(private readonly GraphQlConfigPolicy $graphQl)
            {
            }

            public function configType(): string
            {
                return $this->graphQl->configType();
            }

            public function subjectType(): string
            {
                return $this->graphQl->subjectType();
            }

            public function noun(): string
            {
                return $this->graphQl->noun();
            }

            public function readToolName(): string
            {
                return $this->graphQl->readToolName();
            }

            public function proposable(): array
            {
                return $this->graphQl->proposable();
            }

            public function withheld(): array
            {
                return $this->graphQl->withheld();
            }

            public function withheldReason(): string
            {
                return $this->graphQl->withheldReason();
            }

            public function slots(): array
            {
                return $this->graphQl->slots();
            }

            public function problems(array $state, bool $isNew): array
            {
                return [];
            }

            public function summarize(array $configuration): array
            {
                return $this->graphQl->summarize($configuration);
            }

            public function save(string $name, array $configuration): void
            {
                // nothing to store: the test reads what the session recorded
            }

            public function perEntityPaths(): array
            {
                return $this->graphQl->perEntityPaths();
            }
        };
    }

    private function tools(): ConfigProposalTools
    {
        $user = new User();
        $user->setId(7);
        $user->setPermissions([PermissionConstants::PLUGIN_DATA_HUB_CONFIG]);
        $security = $this->createStub(SecurityServiceInterface::class);
        $security->method('getCurrentUser')->willReturn($user);

        $sessions = $this->createStub(AgentSessionServiceInterface::class);
        $sessions->method('setProposalData')->willReturnCallback(
            function (string $sessionId, string $proposalId, array $data): void {
                $this->recorded[] = $data;
            }
        );

        $resolver = $this->createStub(ProposalResolverInterface::class);
        $resolver->method('getType')->willReturn('subject-update');
        $resolver->method('getWidgetType')->willReturn('config-widget');

        $request = new Request();
        $request->attributes->set(McpAccessTokenAuthenticator::REQUEST_ATTR_REFERENCE, 'session-1');
        $requests = new RequestStack();
        $requests->push($request);

        $adapters = $this->createStub(AdapterAvailabilityServiceInterface::class);
        $adapters->method('isEnabled')->willReturn(true);

        return new ConfigProposalTools(
            $sessions,
            new ProposalWidgetEmitter(
                new ProposalResolverRegistry([$resolver]),
                $this->createStub(TaskContextResolverInterface::class),
            ),
            new BoundSessionReferenceResolver($requests),
            $security,
            $this->createStub(McpToolErrorHandlerInterface::class),
            $this->stored,
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
     */
    private function configuration(string $name, string $type, array $document): Configuration
    {
        $configuration = $this->createStub(Configuration::class);
        $configuration->method('getName')->willReturn($name);
        $configuration->method('getType')->willReturn($type);
        $configuration->method('getConfiguration')->willReturn($document);
        $configuration->method('isAllowed')->willReturn(true);

        return $configuration;
    }
}
