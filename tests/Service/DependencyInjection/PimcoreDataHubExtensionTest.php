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
use Mcp\Capability\Attribute\McpTool;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectHandlerInterface;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\GraphQlConfigPolicy;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\Tool\GetGraphQlConfigTool;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\Tool\ListGraphQlConfigsTool;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\Tool\ProposeGraphQlConfigTool;
use Pimcore\Bundle\DataHubBundle\Proposal\Mcp\ConfigProposalTools;
use ReflectionMethod;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Resource\ClassExistenceResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Yaml\Yaml;

class PimcoreDataHubExtensionTest extends Unit
{
    private const string SKILL = 'data-hub-graphql-configuration';

    private const string SKILLS_DIR = __DIR__ . '/../../../src/Resources/skills';

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

    public function testALaneNeedsItsBundleRegisteredNotOnlyInstalled(): void
    {
        $withoutChangeControl = new ContainerBuilder();
        $withoutChangeControl->setParameter('kernel.bundles', ['PimcoreDataHubBundle' => PimcoreDataHubBundle::class]);
        $this->assertFalse(ConfigProposalLane::canReview($withoutChangeControl));
        $this->assertFalse(ConfigProposalLane::canPropose($withoutChangeControl));

        $withChangeControl = new ContainerBuilder();
        $withChangeControl->setParameter(
            'kernel.bundles',
            ['PimcoreChangeControlBundle' => 'Pimcore\\Bundle\\ChangeControlBundle\\PimcoreChangeControlBundle']
        );
        $this->assertSame(
            interface_exists(SubjectHandlerInterface::class),
            ConfigProposalLane::canReview($withChangeControl)
        );
        $this->assertFalse(ConfigProposalLane::canPropose($withChangeControl), 'the agent bundle is not registered');
    }

    public function testTheGraphQlGroupsAndSkillAreContributedToTheConfigurationAgentWhereAnAgentCanPropose(): void
    {
        $container = $this->prependExtension(self::peerExtension('pimcore_agent', withContributions: true));

        [$config] = $container->getExtensionConfig('pimcore_agent');
        $this->assertSame([realpath(self::SKILLS_DIR)], array_map(realpath(...), $config['skills']['paths']));
        $expected = ConfigProposalLane::canPropose(new ContainerBuilder())
            ? ['configuration' => [
                'pimcoreMcpServers' => ['pimcore-data-hub-graphql-read', 'pimcore-data-hub-graphql-propose'],
                'skills' => [self::SKILL],
            ]]
            : null;
        $this->assertSame($expected, $config['agents']['contributions'] ?? null);
    }

    public function testNothingIsContributedWithoutAnAgentBundle(): void
    {
        $this->assertSame([], $this->prependExtension()->getExtensionConfig('pimcore_agent'));
    }

    /** an agent bundle released before the contributions node would reject the key */
    public function testAnAgentBundleWithoutContributionsGetsTheSkillPathOnly(): void
    {
        [$config] = $this->prependExtension(self::peerExtension('pimcore_agent'))->getExtensionConfig('pimcore_agent');

        $this->assertSame(['skills'], array_keys($config));
    }

    public function testAnAgentBundleWithoutAConfigurationTreeGetsTheSkillPathOnly(): void
    {
        $agent = new class() implements ExtensionInterface {
            public function load(array $configs, ContainerBuilder $container): void
            {
                // only its missing configuration tree matters to the prepend
            }

            public function getNamespace(): string
            {
                return '';
            }

            public function getXsdValidationBasePath(): string|false
            {
                return false;
            }

            public function getAlias(): string
            {
                return 'pimcore_agent';
            }
        };

        [$config] = $this->prependExtension($agent)->getExtensionConfig('pimcore_agent');
        $this->assertSame(['skills'], array_keys($config));
    }

    public function testTheContributedSkillHasAValidSkillFile(): void
    {
        $file = self::SKILLS_DIR . '/' . self::SKILL . '/SKILL.md';
        $this->assertFileExists($file);

        $this->assertSame(1, preg_match('/\A---\n(.*?)\n---\n(.+)\z/s', (string) file_get_contents($file), $parts));
        $frontMatter = Yaml::parse($parts[1]);
        $this->assertSame(self::SKILL, $frontMatter['name'] ?? null);
        $this->assertIsString($frontMatter['description'] ?? null);
        $this->assertStringStartsWith('Use before proposing or changing a', $frontMatter['description']);
        $this->assertNotSame('', trim($parts[2]));
    }

    public function testTheProposeToolPointsAtTheContributedSkill(): void
    {
        $attribute = (new ReflectionMethod(ProposeGraphQlConfigTool::class, 'execute'))
            ->getAttributes(McpTool::class)[0]->newInstance();

        $this->assertStringContainsString('Load the ' . self::SKILL . ' skill', (string) $attribute->description);
    }

    private function prependExtension(ExtensionInterface ...$peers): ContainerBuilder
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
                // only its configuration tree matters to the prepend
            }

            public function getAlias(): string
            {
                return $this->alias;
            }

            public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
            {
                return new class($this->withContributions, $this->alias) implements ConfigurationInterface {
                    public function __construct(
                        private readonly bool $withContributions,
                        private readonly string $alias
                    ) {
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
