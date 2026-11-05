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

namespace Pimcore\Bundle\DataHubBundle\Tests\Service;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;

class WorkspaceHelperTest extends Unit
{
    private const CHANGED_PATH = '/source';

    private const NEW_PATH = '/moved';

    /**
     * @var array<string, mixed>
     */
    private array $configurationData = [];

    public function testExactPathIsReplaced(): void
    {
        $result = $this->replace(self::CHANGED_PATH);

        $this->assertSame([self::NEW_PATH], $this->paths($result));
    }

    public function testDescendantIsReplacedAtTheLeadingPathOnly(): void
    {
        $result = $this->replace('/source/a/source/b');

        $this->assertSame([self::NEW_PATH . '/a/source/b'], $this->paths($result));
    }

    public function testUnrelatedPathContainingTheSegmentIsNotReplaced(): void
    {
        $result = $this->replace('/protected/source/private');

        $this->assertNull($result, 'A path that only contains the changed path must be left alone');
    }

    public function testPathWithTheSamePrefixButAnotherSegmentIsNotReplaced(): void
    {
        $result = $this->replace('/source-archive/a');

        $this->assertNull($result);
    }

    public function testDeleteRemovesExactPathAndDescendants(): void
    {
        $result = $this->delete([self::CHANGED_PATH, '/source/a', '/other']);

        $this->assertSame(['/other'], $this->paths($result));
    }

    public function testDeleteKeepsUnrelatedPathContainingTheSegment(): void
    {
        $result = $this->delete(['/protected/source/private']);

        $this->assertNull($result, 'A path that only contains the deleted path must be left alone');
    }

    private function replace(string $cpath): ?Configuration
    {
        $result = WorkspaceHelper::modifyWorkspaceRowByType(
            $this->createConfiguration([$cpath]),
            WorkspaceHelper::MODIFY_SPACE_OBJECT,
            WorkspaceHelper::MODIFY_TYPE_REPLACE,
            self::CHANGED_PATH,
            self::NEW_PATH
        );

        return $result instanceof Configuration ? $result : null;
    }

    /**
     * @param string[] $cpaths
     */
    private function delete(array $cpaths): ?Configuration
    {
        $result = WorkspaceHelper::modifyWorkspaceRowByType(
            $this->createConfiguration($cpaths),
            WorkspaceHelper::MODIFY_SPACE_OBJECT,
            WorkspaceHelper::MODIFY_TYPE_DELETE,
            self::CHANGED_PATH,
            null
        );

        return $result instanceof Configuration ? $result : null;
    }

    /**
     * @param string[] $cpaths
     */
    private function createConfiguration(array $cpaths): Configuration
    {
        $spaces = array_map(static fn (string $cpath): array => ['cpath' => $cpath], $cpaths);
        $this->configurationData = ['workspaces' => [WorkspaceHelper::MODIFY_SPACE_OBJECT => $spaces]];

        // a stateful mock keeps the test independent of the configuration storage
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getConfiguration')->willReturnCallback(fn (): array => $this->configurationData);
        $configuration->method('setConfiguration')->willReturnCallback(function (?array $data): void {
            $this->configurationData = $data ?? [];
        });

        return $configuration;
    }

    /**
     * @return string[]
     */
    private function paths(?Configuration $configuration): array
    {
        $this->assertInstanceOf(Configuration::class, $configuration);

        $workspaces = $this->configurationData['workspaces'][WorkspaceHelper::MODIFY_SPACE_OBJECT];

        return array_column($workspaces, 'cpath');
    }
}
