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
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\Asset;
use ReflectionProperty;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * updateAsset has to honour the workspace of the configuration: the 'update' right on the asset and, when the input
 * moves the asset, the 'create' right on the new parent.
 */
class UpdateAssetMutationWorkspaceTest extends Unit
{
    private const CONFIG_NAME = 'update_asset_workspace_test';

    private const CACHE_KEY = 'datahub_workspace_permissions_' . self::CONFIG_NAME . '_asset';

    private const ALLOWED_FOLDER_ID = 20;

    private const DENIED_FOLDER_ID = 30;

    private const TARGET_FOLDER_ID = 40;

    private const ASSET_IN_ALLOWED = 201;

    private const ASSET_IN_DENIED = 301;

    private const ASSET_OUTSIDE = 901;

    private ?KernelInterface $previousKernel;

    private int $previousPolicy;

    /** @var array<string, array{bool, mixed}> */
    private array $previousCache = [];

    /** @var array<int, Asset&\PHPUnit\Framework\MockObject\MockObject> */
    private array $assets = [];

    protected function setUp(): void
    {
        $this->previousKernel = \Pimcore::getKernel();
        $this->previousPolicy = PimcoreDataHubBundle::$notAllowedPolicy;
        PimcoreDataHubBundle::setNotAllowedPolicy(PimcoreDataHubBundle::NOT_ALLOWED_POLICY_NULL);

        $container = new Container();
        $container->set('event_dispatcher', new EventDispatcher());
        // building the exception message asks Asset::getFullPath() whether this is a frontend request
        $container->set('request_stack', new RequestStack());
        $kernel = $this->createMock(KernelInterface::class);
        $kernel->method('getContainer')->willReturn($container);
        $this->setKernel($kernel);

        $this->rememberCache(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        $this->rememberCache(self::CACHE_KEY);
        RuntimeCache::set(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY, ['configuration' => new Configuration(
            'graphql',
            '/',
            self::CONFIG_NAME,
            ['security' => ['skipPermissionCheck' => false]]
        )]);

        // workspace: update + create on the allowed folder, read only on the denied one
        RuntimeCache::save([
            self::ALLOWED_FOLDER_ID => $this->row(self::ALLOWED_FOLDER_ID, 1, 1),
            self::DENIED_FOLDER_ID => $this->row(self::DENIED_FOLDER_ID, 0, 0),
        ], self::CACHE_KEY);

        $allowedFolder = $this->asset(self::ALLOWED_FOLDER_ID, 1, null);
        $deniedFolder = $this->asset(self::DENIED_FOLDER_ID, 1, null);
        $targetFolder = $this->asset(self::TARGET_FOLDER_ID, 1, null);
        $this->asset(self::ASSET_IN_ALLOWED, self::ALLOWED_FOLDER_ID, $allowedFolder);
        $this->asset(self::ASSET_IN_DENIED, self::DENIED_FOLDER_ID, $deniedFolder);
        $this->asset(self::ASSET_OUTSIDE, 1, null);
        $this->assets[self::TARGET_FOLDER_ID] = $targetFolder;
    }

    protected function tearDown(): void
    {
        $this->setKernel($this->previousKernel);
        PimcoreDataHubBundle::setNotAllowedPolicy($this->previousPolicy);
        foreach ($this->previousCache as $key => [$wasRegistered, $value]) {
            if ($wasRegistered) {
                RuntimeCache::set($key, $value);
            } elseif (RuntimeCache::isRegistered($key)) {
                RuntimeCache::getInstance()->offsetUnset($key);
            }
        }
    }

    public function testUpdateInAllowedFolderIsApplied(): void
    {
        $result = $this->update(['id' => self::ASSET_IN_ALLOWED, 'input' => ['filename' => 'renamed.txt']]);

        $this->assertTrue($result['success']);
        $this->assertSame('renamed.txt', $this->assets[self::ASSET_IN_ALLOWED]->getFilename());
    }

    public function testUpdateInDeniedFolderIsRejected(): void
    {
        $result = $this->update(['id' => self::ASSET_IN_DENIED, 'input' => ['filename' => 'renamed.txt']]);

        $this->assertFalse($result['success']);
        $this->assertSame('permission denied.', $result['message']);
        $this->assertNotSame('renamed.txt', $this->assets[self::ASSET_IN_DENIED]->getFilename());
    }

    public function testUpdateOutsideAllWorkspaceEntriesIsRejected(): void
    {
        $result = $this->update(['id' => self::ASSET_OUTSIDE, 'input' => ['filename' => 'renamed.txt']]);

        $this->assertFalse($result['success']);
        $this->assertSame('permission denied.', $result['message']);
        $this->assertNotSame('renamed.txt', $this->assets[self::ASSET_OUTSIDE]->getFilename());
    }

    public function testRejectedUpdateNeitherSavesNorChangesData(): void
    {
        $this->assets[self::ASSET_IN_DENIED]->expects($this->never())->method('save');
        $this->assets[self::ASSET_IN_DENIED]->expects($this->never())->method('setData');

        $this->update(['id' => self::ASSET_IN_DENIED, 'input' => ['data' => base64_encode('x')]]);
    }

    public function testUnknownAssetIsReported(): void
    {
        $result = $this->update(['id' => -1, 'input' => ['filename' => 'renamed.txt']]);

        $this->assertFalse($result['success']);
        $this->assertSame('unable to update asset. Unknown id or fullpath', $result['message']);
    }

    public function testMoveToParentWithoutCreateRightIsRejected(): void
    {
        $result = $this->update([
            'id' => self::ASSET_IN_ALLOWED,
            'input' => ['parentId' => self::DENIED_FOLDER_ID],
        ]);

        $this->assertFalse($result['success']);
        $this->assertSame('not allowed to move asset to the given parent', $result['message']);
        $this->assertSame(self::ALLOWED_FOLDER_ID, $this->assets[self::ASSET_IN_ALLOWED]->getParentId());
    }

    public function testMoveToParentOutsideAllWorkspaceEntriesIsRejected(): void
    {
        $result = $this->update([
            'id' => self::ASSET_IN_ALLOWED,
            'input' => ['parentId' => self::TARGET_FOLDER_ID],
        ]);

        $this->assertFalse($result['success']);
        $this->assertSame('not allowed to move asset to the given parent', $result['message']);
        $this->assertSame(self::ALLOWED_FOLDER_ID, $this->assets[self::ASSET_IN_ALLOWED]->getParentId());
    }

    public function testMoveToUnknownParentIsRejected(): void
    {
        $result = $this->update([
            'id' => self::ASSET_IN_ALLOWED,
            'input' => ['parentId' => -1],
        ]);

        $this->assertFalse($result['success']);
        $this->assertSame('unable to resolve parent', $result['message']);
    }

    public function testMoveToParentWithCreateRightIsApplied(): void
    {
        RuntimeCache::save([
            self::ALLOWED_FOLDER_ID => $this->row(self::ALLOWED_FOLDER_ID, 1, 1),
            self::TARGET_FOLDER_ID => $this->row(self::TARGET_FOLDER_ID, 1, 0),
        ], self::CACHE_KEY);

        $result = $this->update([
            'id' => self::ASSET_IN_ALLOWED,
            'input' => ['parentId' => self::TARGET_FOLDER_ID],
        ]);

        $this->assertTrue($result['success']);
        $this->assertSame(self::TARGET_FOLDER_ID, $this->assets[self::ASSET_IN_ALLOWED]->getParentId());
    }

    public function testUnchangedParentNeedsNoCreateRight(): void
    {
        RuntimeCache::save([
            self::ALLOWED_FOLDER_ID => $this->row(self::ALLOWED_FOLDER_ID, 0, 1),
        ], self::CACHE_KEY);

        $result = $this->update([
            'id' => self::ASSET_IN_ALLOWED,
            'input' => ['parentId' => self::ALLOWED_FOLDER_ID],
        ]);

        $this->assertTrue($result['success']);
    }

    public function testOmitPermissionCheckSkipsTheWorkspace(): void
    {
        $result = $this->update(
            ['id' => self::ASSET_IN_DENIED, 'input' => ['filename' => 'renamed.txt']],
            true
        );

        $this->assertTrue($result['success']);
        $this->assertSame('renamed.txt', $this->assets[self::ASSET_IN_DENIED]->getFilename());
    }

    public function testExceptionPolicyThrowsForDeniedAsset(): void
    {
        PimcoreDataHubBundle::setNotAllowedPolicy(PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION);

        $this->expectException(ClientSafeException::class);

        $this->update(['id' => self::ASSET_IN_DENIED, 'input' => ['filename' => 'renamed.txt']]);
    }

    /**
     * @return array<string, mixed>
     */
    private function update(array $args, bool $omitPermissionCheck = false): array
    {
        // the service is final and needs the whole container: set just the two lookups the mutation builder uses
        $graphQlService = (new \ReflectionClass(Service::class))->newInstanceWithoutConstructor();
        $assetTypes = new Container();
        $assetTypes->set('asset', new class() {
            public function build(): Type
            {
                return Type::string();
            }
        });
        $this->setProperty($graphQlService, 'assetTypeGeneratorFactories', $assetTypes);
        $this->setProperty($graphQlService, 'assetDataTypes', ['asset_input' => Type::string()]);

        // the constructor would need the whole service container; the mutation is built by hand instead
        $mutationType = (new \ReflectionClass(MutationType::class))->newInstanceWithoutConstructor();
        $mutationType->setGraphQLService($graphQlService);
        $mutationType->setOmitPermissionCheck($omitPermissionCheck);
        $this->setProperty($mutationType, 'eventDispatcher', new EventDispatcher());

        $config = ['fields' => []];
        $configuration = new Configuration('graphql', '/', self::CONFIG_NAME, [
            'schema' => ['specialEntities' => ['asset' => ['update' => true]]],
        ]);
        $mutationType->buildUpdateAssetMutation($config, ['configuration' => $configuration]);

        $resolve = $config['fields']['updateAsset']['resolve'];

        return $resolve(null, $args, [], $this->createMock(ResolveInfo::class));
    }

    private function row(int $id, int $create, int $update): array
    {
        return ['cid' => $id, 'cpath' => '/' . $id, 'create' => $create, 'read' => 1, 'update' => $update, 'delete' => 0];
    }

    /**
     * An in-memory asset that Asset::getById() finds in the runtime cache; saving is a no-op.
     */
    private function asset(int $id, int $parentId, ?Asset $parent): Asset
    {
        $asset = $this->getMockBuilder(Asset\Text::class)->onlyMethods(['save', 'setData', 'getParent'])->getMock();
        $asset->method('getParent')->willReturn($parent);
        $asset->setId($id);
        $asset->setParentId($parentId);
        $asset->setFilename('asset-' . $id . '.txt');
        $asset->setPath('/');

        $key = 'asset_' . $id;
        $this->rememberCache($key);
        RuntimeCache::set($key, $asset);
        $this->assets[$id] = $asset;

        return $asset;
    }

    private function rememberCache(string $key): void
    {
        $registered = RuntimeCache::isRegistered($key);
        $this->previousCache[$key] ??= [$registered, $registered ? RuntimeCache::get($key) : null];
    }

    private function setProperty(object $object, string $name, mixed $value): void
    {
        (new ReflectionProperty($object, $name))->setValue($object, $value);
    }

    private function setKernel(?KernelInterface $kernel): void
    {
        $property = new ReflectionProperty(\Pimcore::class, 'kernel');
        $property->setValue(null, $kernel);
    }
}
