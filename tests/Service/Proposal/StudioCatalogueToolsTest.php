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

use function array_diff;
use function array_keys;
use function array_values;
use function class_exists;
use Codeception\Test\Unit;
use function in_array;
use Mcp\Capability\Attribute\McpTool;
use Pimcore\Bundle\StudioBackendBundle\DependencyInjection\CompilerPass\McpToolPass;
use Pimcore\Bundle\StudioBackendBundle\Mcp\Registry\McpToolRegistry;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use function sort;
use function sprintf;
use function str_starts_with;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use function trim;

/**
 * Tools tagged for the Studio catalogue are served on Studio-managed MCP servers, whose clients carry no agent chat
 * session: only session-free tools may carry the tag, and the server derives its OAuth scope from readOnlyHint.
 */
final class StudioCatalogueToolsTest extends Unit
{
    private const string STUDIO_TAG = 'pimcore.studio_backend.mcp_tool';

    private const string AGENT_TAG = 'pimcore.mcp_tool';

    private const string CONFIG_DIR = __DIR__ . '/../../../src/Resources/config';

    private const string CONFIG_FILE = 'proposal/graphql_mcp.yml';

    /** types whose presence means the tool resolves the agent chat session */
    private const array SESSION_BOUND_TYPES = [
        'Pimcore\\Bundle\\PimcoreAgentBundle\\Security\\BoundSessionReferenceResolver',
        'Pimcore\\Bundle\\PimcoreAgentBundle\\Service\\AgentSessionServiceInterface',
        'Pimcore\\Bundle\\PimcoreAgentBundle\\Proposal\\ProposalWidgetEmitter',
    ];

    /** the reviewed selection; extending it is a deliberate change to this list */
    private const array CATALOGUE_TOOLS = ['get_graphql_config', 'list_graphql_configs'];

    /** the proposal is recorded in the chat session and reviewed on its card */
    private const array CHAT_ONLY_TOOLS = ['propose_graphql_config'];

    public function testTheCatalogueCarriesExactlyTheReviewedTools(): void
    {
        $this->assertSame(self::CATALOGUE_TOOLS, $this->names(self::STUDIO_TAG));
    }

    public function testEveryOtherAgentToolIsChatOnly(): void
    {
        $this->assertSame(
            self::CHAT_ONLY_TOOLS,
            array_values(array_diff($this->names(self::AGENT_TAG), self::CATALOGUE_TOOLS))
        );
    }

    public function testCatalogueToolsNeedNoChatSession(): void
    {
        foreach ($this->tools(self::STUDIO_TAG) as $name => [$class]) {
            $this->assertFalse(str_starts_with($name, 'propose_'), sprintf('%s is a proposal', $name));
            foreach ((new ReflectionClass($class))->getConstructor()?->getParameters() ?? [] as $parameter) {
                $type = $parameter->getType();
                $this->assertFalse(
                    $type instanceof ReflectionNamedType && in_array($type->getName(), self::SESSION_BOUND_TYPES, true),
                    sprintf('%s (%s) resolves the agent chat session', $name, $class)
                );
            }
        }
    }

    public function testCatalogueToolsAreReadOnlyWithATitleAndADescription(): void
    {
        foreach ($this->tools(self::STUDIO_TAG) as $name => [, $tool]) {
            $this->assertTrue($tool->annotations?->readOnlyHint, sprintf('%s must declare readOnlyHint: true', $name));
            $this->assertNotSame('', trim((string) $tool->title), sprintf('%s needs a title', $name));
            $this->assertNotSame('', trim((string) $tool->description), sprintf('%s needs a description', $name));
        }
    }

    public function testTheStudioRegistryCollectsTheCatalogueTools(): void
    {
        if (!class_exists(McpToolPass::class)) {
            $this->markTestSkipped('needs a Studio backend with the MCP tool catalogue');
        }

        $container = $this->container();
        $container->register(McpToolRegistry::class, McpToolRegistry::class);
        (new McpToolPass())->process($container);

        $metadata = $container->getDefinition(McpToolRegistry::class)->getArgument('$toolMetadata');
        $names = array_keys($metadata);
        sort($names);
        $this->assertSame(self::CATALOGUE_TOOLS, $names);
        foreach ($metadata as $name => $tool) {
            $this->assertTrue($tool['annotations']['readOnlyHint'] ?? null, sprintf('%s is not read-only', $name));
        }
    }

    /**
     * @return list<string>
     */
    private function names(string $tag): array
    {
        $names = array_keys($this->tools($tag));
        sort($names);

        return $names;
    }

    /**
     * @return array<string, array{class-string, McpTool}>
     */
    private function tools(string $tag): array
    {
        $container = $this->container();
        $tools = [];
        foreach ($container->findTaggedServiceIds($tag) as $id => $tags) {
            /** @var class-string $class */
            $class = $container->getDefinition($id)->getClass() ?? $id;
            foreach ((new ReflectionClass($class))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                foreach ($method->getAttributes(McpTool::class) as $attribute) {
                    $tool = $attribute->newInstance();
                    $tools[$tool->name ?? $method->getName()] = [$class, $tool];
                }
            }
        }

        return $tools;
    }

    private function container(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        (new YamlFileLoader($container, new FileLocator(self::CONFIG_DIR)))->load(self::CONFIG_FILE);

        return $container;
    }
}
