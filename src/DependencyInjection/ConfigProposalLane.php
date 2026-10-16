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

use Mcp\Capability\Attribute\McpTool;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectHandlerInterface;
use Pimcore\Bundle\PimcoreAgentBundle\Proposal\ProposalWidgetEmitter;
use Pimcore\Bundle\StudioBackendBundle\Mcp\Tool\McpToolErrorHandlerInterface;
use Symfony\Component\Config\Resource\ClassExistenceResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Whether an adapter's proposal lane can be wired. Change Control, an MCP host and the agent
 * bundle are all optional peers, so each check is recorded as a container resource and a
 * missing one skips the wiring rather than failing the build.
 */
final class ConfigProposalLane
{
    /** Change Control is installed, so a configuration can be a reviewed subject */
    public static function canReview(ContainerBuilder $container): bool
    {
        return self::exists($container, SubjectHandlerInterface::class);
    }

    /** an agent can also author proposals: there is something to review them with and to record them in */
    public static function canPropose(ContainerBuilder $container): bool
    {
        return self::canReview($container)
            && self::exists($container, McpTool::class)
            && self::exists($container, McpToolErrorHandlerInterface::class)
            && self::exists($container, ProposalWidgetEmitter::class);
    }

    private static function exists(ContainerBuilder $container, string $class): bool
    {
        $container->addResource(new ClassExistenceResource($class));

        return class_exists($class) || interface_exists($class);
    }
}
