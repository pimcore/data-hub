<?php

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
    /**
     * @var array<string, mixed>
     */
    private array $configurationData = [];

    public function testExactPathIsReplaced(): void
    {
        $result = $this->modify('/source', WorkspaceHelper::MODIFY_TYPE_REPLACE, '/source', '/moved');

        $this->assertSame(['/moved'], $this->paths($result));
    }

    public function testDescendantIsReplacedAtTheLeadingPathOnly(): void
    {
        $result = $this->modify('/source/a/source/b', WorkspaceHelper::MODIFY_TYPE_REPLACE, '/source', '/moved');

        $this->assertSame(['/moved/a/source/b'], $this->paths($result));
    }

    public function testUnrelatedPathContainingTheSegmentIsNotReplaced(): void
    {
        $result = $this->modify('/protected/source/private', WorkspaceHelper::MODIFY_TYPE_REPLACE, '/source', '/moved');

        $this->assertNull($result, 'A path that only contains the changed path must be left alone');
    }

    public function testPathWithTheSamePrefixButAnotherSegmentIsNotReplaced(): void
    {
        $result = $this->modify('/source-archive/a', WorkspaceHelper::MODIFY_TYPE_REPLACE, '/source', '/moved');

        $this->assertNull($result);
    }

    public function testDeleteRemovesExactPathAndDescendants(): void
    {
        $configuration = $this->createConfiguration(['/source', '/source/a', '/other']);

        $result = WorkspaceHelper::modifyWorkspaceRowByType(
            $configuration,
            WorkspaceHelper::MODIFY_SPACE_OBJECT,
            WorkspaceHelper::MODIFY_TYPE_DELETE,
            '/source',
            null
        );

        $this->assertSame(['/other'], $this->paths($result));
    }

    public function testDeleteKeepsUnrelatedPathContainingTheSegment(): void
    {
        $result = $this->modify('/protected/source/private', WorkspaceHelper::MODIFY_TYPE_DELETE, '/source', null);

        $this->assertNull($result, 'A path that only contains the deleted path must be left alone');
    }

    private function modify(string $cpath, string $modificationType, string $searchValue, ?string $replaceValue): ?Configuration
    {
        $configuration = $this->createConfiguration([$cpath]);

        $result = WorkspaceHelper::modifyWorkspaceRowByType(
            $configuration,
            WorkspaceHelper::MODIFY_SPACE_OBJECT,
            $modificationType,
            $searchValue,
            $replaceValue
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

        return array_column($this->configurationData['workspaces'][WorkspaceHelper::MODIFY_SPACE_OBJECT], 'cpath');
    }
}
