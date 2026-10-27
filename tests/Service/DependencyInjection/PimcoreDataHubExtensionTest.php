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
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\Proposal\Mcp\ConfigProposalTools;
use Symfony\Component\Config\Resource\ClassExistenceResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
        $withChangeControl->setParameter('kernel.bundles', ['PimcoreChangeControlBundle' => 'Pimcore\\Bundle\\ChangeControlBundle\\PimcoreChangeControlBundle']);
        $this->assertSame(interface_exists(SubjectHandlerInterface::class), ConfigProposalLane::canReview($withChangeControl));
        $this->assertFalse(ConfigProposalLane::canPropose($withChangeControl), 'the agent bundle is not registered');
    }

    private function loadExtension(array $configs): ContainerBuilder
    {
        $container = new ContainerBuilder();
        (new PimcoreDataHubExtension())->load($configs, $container);

        return $container;
    }
}
