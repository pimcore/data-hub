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

namespace Pimcore\Bundle\DataHubBundle\DependencyInjection;

use Codeception\Test\Unit;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectHandlerInterface;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\GraphQlConfigPolicy;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\Tool\GetGraphQlConfigTool;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\Tool\ListGraphQlConfigsTool;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\Tool\ProposeGraphQlConfigTool;
use Pimcore\Bundle\DataHubBundle\Proposal\Mcp\ConfigProposalTools;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Resource\ClassExistenceResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class PimcoreDataHubExtensionTest extends Unit
{
    private const array SUPPORTED_TYPES = [
        'supported_types' => [
            'graphql' => 'Pimcore\Bundle\DataHubBundle',
            'simpleRest' => 'Pimcore\Bundle\DataHubSimpleRestBundle',
        ],
    ];

    public function testGraphQlIsEnabledByDefault(): void
    {
        $container = $this->loadExtension([self::SUPPORTED_TYPES]);

        $this->assertTrue($container->getParameter('pimcore_data_hub')['graphql']['enabled']);
        $this->assertSame(
            ['graphql', 'simpleRest'],
            $container->getParameter('pimcore_data_hub.enabled_adapter_types')
        );
    }

    public function testSwitchingOffGraphQlLeavesOtherAdapterTypesAlone(): void
    {
        $container = $this->loadExtension([self::SUPPORTED_TYPES, ['graphql' => ['enabled' => false]]]);

        $this->assertFalse($container->getParameter('pimcore_data_hub')['graphql']['enabled']);
        $this->assertSame(
            ['simpleRest'],
            $container->getParameter('pimcore_data_hub.enabled_adapter_types')
        );
    }

    public function testTheSupportedTypesKeepReportingWhichBundlesAreInstalled(): void
    {
        $container = $this->loadExtension([self::SUPPORTED_TYPES, ['graphql' => ['enabled' => false]]]);

        $this->assertSame(
            self::SUPPORTED_TYPES['supported_types'],
            $container->getParameter('pimcore_data_hub')['supported_types']
        );
    }

    public function testNoTypeIsEnabledWithoutSupportedTypes(): void
    {
        $container = $this->loadExtension([]);

        $this->assertSame([], $container->getParameter('pimcore_data_hub.enabled_adapter_types'));
    }

    public function testTheProposalToolsAreWiredOnlyWhereAnAgentCanPropose(): void
    {
        $container = $this->loadExtension([self::SUPPORTED_TYPES]);

        $this->assertSame(
            ConfigProposalLane::canPropose(new ContainerBuilder()),
            $container->hasDefinition(ConfigProposalTools::class)
        );
    }

    public function testTheGraphQlReviewLaneIsWiredOnlyWhereChangeControlCanReviewIt(): void
    {
        $container = $this->loadExtension([self::SUPPORTED_TYPES]);
        $canReview = ConfigProposalLane::canReview(new ContainerBuilder());

        $this->assertSame($canReview, $container->hasDefinition(GraphQlConfigPolicy::class));
        $this->assertSame(
            $canReview,
            $container->hasDefinition('pimcore.datahub.graphql.proposal.subject_handler')
        );
        $this->assertSame(
            $canReview,
            $container->hasDefinition('pimcore.datahub.graphql.proposal.detail_hydrator')
        );
    }

    public function testTheGraphQlToolsAreWiredOnlyWhereAnAgentCanPropose(): void
    {
        $container = $this->loadExtension([self::SUPPORTED_TYPES]);
        $canPropose = ConfigProposalLane::canPropose(new ContainerBuilder());

        $tools = [ListGraphQlConfigsTool::class, GetGraphQlConfigTool::class, ProposeGraphQlConfigTool::class];
        foreach ($tools as $tool) {
            $this->assertSame($canPropose, $container->hasDefinition($tool), $tool);
        }
    }

    public function testEachLaneCheckIsRecordedAsAContainerResource(): void
    {
        $container = new ContainerBuilder();

        $this->assertSame(
            interface_exists(SubjectHandlerInterface::class),
            ConfigProposalLane::canReview($container)
        );
        $this->assertContainsOnlyInstancesOf(ClassExistenceResource::class, $container->getResources());
        $this->assertNotEmpty($container->getResources());
    }

    public function testTheGraphQlGroupsAreContributedToTheConfigurationAgentWhereAnAgentCanPropose(): void
    {
        $container = $this->prependExtension(self::peerExtension('pimcore_agent', withContributions: true));

        $expected = ConfigProposalLane::canPropose(new ContainerBuilder())
            ? [['agents' => ['contributions' => ['configuration' => [
                'pimcoreMcpServers' => ['pimcore-data-hub-graphql-read', 'pimcore-data-hub-graphql-propose'],
            ]]]]]
            : [];
        $this->assertSame($expected, $container->getExtensionConfig('pimcore_agent'));
    }

    public function testNothingIsContributedWithoutAnAgentBundleThatTakesContributions(): void
    {
        $this->assertSame([], $this->prependExtension()->getExtensionConfig('pimcore_agent'));
        $this->assertSame(
            [],
            $this->prependExtension(self::peerExtension('pimcore_agent'))->getExtensionConfig('pimcore_agent')
        );
    }

    private function prependExtension(Extension ...$peers): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $extension = new PimcoreDataHubExtension();
        $container->registerExtension($extension);
        $container->registerExtension(self::peerExtension('pimcore_studio_backend'));
        foreach ($peers as $peer) {
            $container->registerExtension($peer);
        }
        $extension->prepend($container);

        return $container;
    }

    private static function peerExtension(string $alias, bool $withContributions = false): Extension
    {
        return new class($withContributions, $alias) extends Extension {
            public function __construct(private readonly bool $withContributions, private readonly string $alias)
            {
            }

            public function load(array $configs, ContainerBuilder $container): void
            {
            }

            public function getAlias(): string
            {
                return $this->alias;
            }

            public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
            {
                return new class($this->withContributions, $this->alias) implements ConfigurationInterface {
                    public function __construct(private readonly bool $withContributions, private readonly string $alias)
                    {
                    }

                    public function getConfigTreeBuilder(): TreeBuilder
                    {
                        $treeBuilder = new TreeBuilder($this->alias);
                        $agents = $treeBuilder->getRootNode()->children()->arrayNode('agents')->children();
                        if ($this->withContributions) {
                            $agents->arrayNode('contributions')->variablePrototype()->end()->end();
                        }

                        return $treeBuilder;
                    }
                };
            }
        };
    }

    private function loadExtension(array $configs): ContainerBuilder
    {
        $container = new ContainerBuilder();
        (new PimcoreDataHubExtension())->load($configs, $container);

        return $container;
    }
}
