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
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\GraphQlWorkspacePaths;

/**
 * The shape checks run before any path is looked up, so these need no installation.
 */
class GraphQlWorkspacePathsTest extends Unit
{
    public function testAWorkspaceContainerThatIsNoObjectIsRefused(): void
    {
        $this->assertSame(
            ['workspaces needs the workspace types keyed by type, e.g. {"object": [{"cpath": "/", "read": true}]}.'],
            (new GraphQlWorkspacePaths())->unresolved(['workspaces' => 'invalid']),
        );
    }

    public function testEntriesOfATypeThatAreNoListAreRefused(): void
    {
        $this->assertSame(
            [
                'workspaces.object needs a list of entries, e.g. [{"cpath": "/", "read": true}].',
                'workspaces.asset needs a list of entries, e.g. [{"cpath": "/", "read": true}].',
                'workspaces.document needs a list of entries, e.g. [{"cpath": "/", "read": true}].',
            ],
            (new GraphQlWorkspacePaths())->unresolved(['workspaces' => [
                'object' => 'invalid',
                'asset' => ['first' => ['cpath' => '/', 'read' => true]],
                'document' => true,
            ]]),
        );
    }

    public function testNoWorkspacesOrEmptyListsAreFine(): void
    {
        $paths = new GraphQlWorkspacePaths();

        $this->assertSame([], $paths->unresolved([]));
        $this->assertSame([], $paths->unresolved(['workspaces' => null]));
        $this->assertSame([], $paths->unresolved(['workspaces' => ['object' => [], 'asset' => [], 'document' => []]]));
    }
}
