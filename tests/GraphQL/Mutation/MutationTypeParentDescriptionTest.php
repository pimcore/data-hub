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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\Mutation;

use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Tests\Support\Util\TestHelper;

/**
 * The "not allowed to create ..." messages name the parent the caller asked for,
 * so a wrong value passed through indirection is easy to spot.
 */
class MutationTypeParentDescriptionTest extends Unit
{
    private mixed $backupContext = null;

    private ?ElementInterface $parent = null;

    protected function setUp(): void
    {
        if (RuntimeCache::isRegistered(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY)) {
            $this->backupContext = RuntimeCache::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        }

        // a configuration without workspace entries denies every create
        $configuration = new Configuration('graphql', '/1', 'datahub-parent-description-test');
        $configuration->setConfiguration([]);
        RuntimeCache::set(
            PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY,
            ['clientname' => 'datahub-parent-description-test', 'configuration' => $configuration]
        );
    }

    protected function tearDown(): void
    {
        if ($this->backupContext !== null) {
            RuntimeCache::set(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY, $this->backupContext);
        }

        $this->parent?->delete();
        $this->parent = null;
    }

    public static function parentArgs(): array
    {
        return [
            'parent id' => [['parentId' => 5], ' in parent id 5'],
            'path' => [['path' => '/News/2026'], " in parent path '/News/2026'"],
            'parent id wins over path' => [['parentId' => 5, 'path' => '/News'], ' in parent id 5'],
            'neither' => [[], ''],
        ];
    }

    #[DataProvider('parentArgs')]
    public function testDescribeParent(array $args, string $expected): void
    {
        $method = new \ReflectionMethod(MutationType::class, 'describeParent');

        $this->assertSame($expected, $method->invoke(null, $args));
    }

    public static function elementTypes(): array
    {
        return [
            'asset' => ['asset', 'assetfolder'],
            'object' => ['object', 'objectfolder'],
            'document' => ['document', 'documentfolder'],
        ];
    }

    /**
     * Guards that the folder resolver appends the parent to its message, not only that the helper works.
     */
    #[DataProvider('elementTypes')]
    public function testCreateFolderDeniedMessageNamesParentIdAndPath(string $elementType, string $folderType): void
    {
        $this->parent = match ($elementType) {
            'asset' => TestHelper::createAssetFolder('pme_t0444_'),
            'object' => TestHelper::createObjectFolder('pme_t0444_'),
            'document' => TestHelper::createDocumentFolder('pme_t0444_'),
        };

        $resolver = $this->createFolderResolver($elementType);

        $byId = $resolver(null, ['parentId' => $this->parent->getId(), 'key' => 'x', 'filename' => 'x'], [], $this->createStub(ResolveInfo::class));
        $this->assertFalse($byId['success']);
        $this->assertSame('not allowed to create ' . $folderType . ' in parent id ' . $this->parent->getId(), $byId['message']);

        $path = $this->parent->getRealFullPath();
        $byPath = $resolver(null, ['path' => $path, 'key' => 'x', 'filename' => 'x'], [], $this->createStub(ResolveInfo::class));
        $this->assertFalse($byPath['success']);
        $this->assertSame('not allowed to create ' . $folderType . " in parent path '" . $path . "'", $byPath['message']);
    }

    private function createFolderResolver(string $elementType): callable
    {
        // the constructor builds the whole schema; the folder resolver only needs the (default) permission check flag
        $mutationType = (new \ReflectionClass(MutationType::class))->newInstanceWithoutConstructor();

        return $mutationType->getCreateFolderResolver($elementType);
    }
}
