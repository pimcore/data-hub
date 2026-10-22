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
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Element\ElementInterface;
use ReflectionProperty;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Mutations that change the parent of an element (updateAssetFolder, updateObjectFolder, updateDocumentFolder,
 * updateObject, createAsset) have to check the workspace for the 'update' right on the element where it is and
 * for the 'create' right on the parent it ends up in, before anything is applied. The folder mutations (also
 * deleteAssetFolder, deleteObjectFolder, deleteDocumentFolder) only act on folders.
 */
class MutationParentWorkspaceTest extends Unit
{
    private const CONFIG_NAME = 'mutation_parent_workspace_test';

    private const ALLOWED_FOLDER_ID = 20;

    private const DENIED_FOLDER_ID = 30;

    private const TARGET_FOLDER_ID = 40;

    private const OUTSIDE_FOLDER_ID = 50;

    private const ELEMENT_IN_ALLOWED = 201;

    private const FOLDER_IN_ALLOWED = 202;

    private const CREATE_ASSET_DENIED = 'not allowed to create asset';

    private ?KernelInterface $previousKernel;

    private int $previousPolicy;

    /** @var array<string, array{bool, mixed}> */
    private array $previousCache = [];

    /** @var array<string, ElementInterface&\PHPUnit\Framework\MockObject\MockObject> */
    private array $elements = [];

    protected function setUp(): void
    {
        $this->previousKernel = \Pimcore::getKernel();
        $this->previousPolicy = PimcoreDataHubBundle::$notAllowedPolicy;
        PimcoreDataHubBundle::setNotAllowedPolicy(PimcoreDataHubBundle::NOT_ALLOWED_POLICY_NULL);

        $container = new Container();
        $container->set('event_dispatcher', new EventDispatcher());
        $container->set('request_stack', new RequestStack());
        $kernel = $this->createMock(KernelInterface::class);
        $kernel->method('getContainer')->willReturn($container);
        $this->setKernel($kernel);

        $this->rememberCache(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        RuntimeCache::set(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY, ['configuration' => $this->configuration()]);

        // workspace: update + create on the allowed folder, create only on the target, read only on the denied one
        foreach (['asset', 'object', 'document'] as $type) {
            $key = 'datahub_workspace_permissions_' . self::CONFIG_NAME . '_' . $type;
            $this->rememberCache($key);
            RuntimeCache::save([
                self::ALLOWED_FOLDER_ID => $this->row(self::ALLOWED_FOLDER_ID, 1, 1, 1),
                self::TARGET_FOLDER_ID => $this->row(self::TARGET_FOLDER_ID, 1, 0),
                self::DENIED_FOLDER_ID => $this->row(self::DENIED_FOLDER_ID, 0, 0),
            ], $key);
        }

        $folderClasses = [
            'asset' => Asset\Folder::class,
            'object' => DataObject\Folder::class,
            'document' => Document\Folder::class,
        ];
        foreach ($folderClasses as $type => $folderClass) {
            $allowed = $this->element($type, $folderClass, self::ALLOWED_FOLDER_ID, 1, null);
            $this->element($type, $folderClass, self::DENIED_FOLDER_ID, 1, null);
            $this->element($type, $folderClass, self::TARGET_FOLDER_ID, 1, null);
            $this->element($type, $folderClass, self::OUTSIDE_FOLDER_ID, 1, null);
            $this->element($type, $folderClass, self::FOLDER_IN_ALLOWED, self::ALLOWED_FOLDER_ID, $allowed);
        }
        $allowedAsset = $this->elements['asset' . self::ALLOWED_FOLDER_ID];
        $deniedAsset = $this->elements['asset' . self::DENIED_FOLDER_ID];
        $allowedObject = $this->elements['object' . self::ALLOWED_FOLDER_ID];
        $deniedObject = $this->elements['object' . self::DENIED_FOLDER_ID];
        $this->element('asset', Asset\Text::class, self::ELEMENT_IN_ALLOWED, self::ALLOWED_FOLDER_ID, $allowedAsset);
        $this->element('asset', Asset\Text::class, 302, self::DENIED_FOLDER_ID, $deniedAsset);
        $this->element(
            'object',
            DataObject\Concrete::class,
            self::ELEMENT_IN_ALLOWED,
            self::ALLOWED_FOLDER_ID,
            $allowedObject
        );
        $this->element('object', DataObject\Concrete::class, 302, self::DENIED_FOLDER_ID, $deniedObject);
        $this->element(
            'document',
            Document\Page::class,
            self::ELEMENT_IN_ALLOWED,
            self::ALLOWED_FOLDER_ID,
            $this->elements['document' . self::ALLOWED_FOLDER_ID]
        );
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

    /**
     * @return array<string, array{string}>
     */
    public static function folderTypes(): array
    {
        return ['asset folder' => ['asset'], 'object folder' => ['object'], 'document folder' => ['document']];
    }

    /**
     * @dataProvider folderTypes
     */
    public function testFolderInAllowedFolderCanBeRenamed(string $type): void
    {
        $key = $type === 'asset' ? 'filename' : 'key';
        $this->elements[$type . self::FOLDER_IN_ALLOWED]->expects($this->once())->method('save');

        $result = $this->updateFolder($type, ['id' => self::FOLDER_IN_ALLOWED, 'input' => [$key => 'renamed']]);

        $this->assertTrue($result['success']);
    }

    /**
     * @dataProvider folderTypes
     */
    public function testFolderCannotBeMovedToParentWithoutCreateRight(string $type): void
    {
        $folder = $this->elements[$type . self::FOLDER_IN_ALLOWED];
        $folder->expects($this->never())->method('save');

        $result = $this->updateFolder(
            $type,
            ['id' => self::FOLDER_IN_ALLOWED, 'input' => ['parentId' => self::DENIED_FOLDER_ID]]
        );

        $this->assertFalse($result['success']);
        $this->assertSame(self::ALLOWED_FOLDER_ID, $folder->getParentId());
    }

    /**
     * @dataProvider folderTypes
     */
    public function testFolderCannotBeMovedToParentOutsideTheWorkspace(string $type): void
    {
        $folder = $this->elements[$type . self::FOLDER_IN_ALLOWED];
        $folder->expects($this->never())->method('save');

        $result = $this->updateFolder(
            $type,
            ['id' => self::FOLDER_IN_ALLOWED, 'input' => ['parentId' => self::OUTSIDE_FOLDER_ID]]
        );

        $this->assertFalse($result['success']);
        $this->assertSame(self::ALLOWED_FOLDER_ID, $folder->getParentId());
    }

    /**
     * @dataProvider folderTypes
     */
    public function testFolderCanBeMovedToParentWithCreateRight(string $type): void
    {
        $folder = $this->elements[$type . self::FOLDER_IN_ALLOWED];
        $folder->expects($this->once())->method('save');

        $result = $this->updateFolder(
            $type,
            ['id' => self::FOLDER_IN_ALLOWED, 'input' => ['parentId' => self::TARGET_FOLDER_ID]]
        );

        $this->assertTrue($result['success']);
        $this->assertSame(self::TARGET_FOLDER_ID, $folder->getParentId());
    }

    /**
     * @dataProvider folderTypes
     */
    public function testFolderMoveToUnknownParentIsRejected(string $type): void
    {
        $folder = $this->elements[$type . self::FOLDER_IN_ALLOWED];
        $folder->expects($this->never())->method('save');

        $result = $this->updateFolder($type, ['id' => self::FOLDER_IN_ALLOWED, 'input' => ['parentId' => -1]]);

        $this->assertFalse($result['success']);
        $this->assertSame(self::ALLOWED_FOLDER_ID, $folder->getParentId());
    }

    /**
     * @dataProvider folderTypes
     */
    public function testUnchangedParentNeedsNoCreateRight(string $type): void
    {
        $this->restrictAllowedFolderToUpdate($type);

        $result = $this->updateFolder(
            $type,
            ['id' => self::FOLDER_IN_ALLOWED, 'input' => ['parentId' => self::ALLOWED_FOLDER_ID]]
        );

        $this->assertTrue($result['success']);
    }

    /**
     * @dataProvider folderTypes
     */
    public function testFolderMutationDoesNotTouchElementsThatAreNoFolders(string $type): void
    {
        $element = $this->elements[$type . self::ELEMENT_IN_ALLOWED];
        $element->expects($this->never())->method('save');

        $result = $this->updateFolder(
            $type,
            ['id' => self::ELEMENT_IN_ALLOWED, 'input' => ['parentId' => self::TARGET_FOLDER_ID]]
        );

        $this->assertFalse($result['success']);
        $this->assertSame(self::ALLOWED_FOLDER_ID, $element->getParentId());
    }

    /**
     * @dataProvider folderTypes
     */
    public function testFolderMutationOmitPermissionCheckSkipsTheWorkspace(string $type): void
    {
        $result = $this->updateFolder(
            $type,
            ['id' => self::FOLDER_IN_ALLOWED, 'input' => ['parentId' => self::DENIED_FOLDER_ID]],
            true
        );

        $this->assertTrue($result['success']);
    }

    /**
     * @dataProvider folderTypes
     */
    public function testFolderInAllowedFolderCanBeDeleted(string $type): void
    {
        $this->elements[$type . self::FOLDER_IN_ALLOWED]->expects($this->once())->method('delete');

        $result = $this->deleteFolder($type, ['id' => self::FOLDER_IN_ALLOWED]);

        $this->assertTrue($result['success']);
    }

    /**
     * @dataProvider folderTypes
     */
    public function testFolderOutsideTheWorkspaceCannotBeDeleted(string $type): void
    {
        $this->elements[$type . self::OUTSIDE_FOLDER_ID]->expects($this->never())->method('delete');

        $result = $this->deleteFolder($type, ['id' => self::OUTSIDE_FOLDER_ID]);

        $this->assertFalse($result['success']);
    }

    /**
     * @dataProvider folderTypes
     */
    public function testFolderDeleteDoesNotTouchElementsThatAreNoFolders(string $type): void
    {
        $this->elements[$type . self::ELEMENT_IN_ALLOWED]->expects($this->never())->method('delete');

        $result = $this->deleteFolder($type, ['id' => self::ELEMENT_IN_ALLOWED]);

        $this->assertFalse($result['success']);
    }

    /**
     * @dataProvider folderTypes
     */
    public function testFolderDeleteWithUnknownIdIsRejected(string $type): void
    {
        $result = $this->deleteFolder($type, ['id' => -1]);

        $this->assertFalse($result['success']);
    }

    /**
     * @dataProvider folderTypes
     */
    public function testFolderDeleteOmitPermissionCheckSkipsTheWorkspace(string $type): void
    {
        $this->elements[$type . self::OUTSIDE_FOLDER_ID]->expects($this->once())->method('delete');

        $result = $this->deleteFolder($type, ['id' => self::OUTSIDE_FOLDER_ID], true);

        $this->assertTrue($result['success']);
    }

    public function testUpdateObjectInDeniedFolderCannotBeMovedIntoAllowedFolder(): void
    {
        $object = $this->elements['object302'];
        $object->expects($this->never())->method('save');

        $result = $this->updateObject(['id' => 302, 'parentId' => self::ALLOWED_FOLDER_ID]);

        $this->assertFalse($result['success']);
        $this->assertSame('permission denied.', $result['message']);
        $this->assertSame(self::DENIED_FOLDER_ID, $object->getParentId());
    }

    public function testUpdateObjectCannotBeMovedToParentWithoutCreateRight(): void
    {
        $object = $this->elements['object' . self::ELEMENT_IN_ALLOWED];
        $object->expects($this->never())->method('save');

        $result = $this->updateObject(['id' => self::ELEMENT_IN_ALLOWED, 'parentId' => self::DENIED_FOLDER_ID]);

        $this->assertFalse($result['success']);
        $this->assertSame(self::ALLOWED_FOLDER_ID, $object->getParentId());
    }

    public function testUpdateObjectCanBeMovedToParentWithCreateRight(): void
    {
        $object = $this->elements['object' . self::ELEMENT_IN_ALLOWED];
        $object->expects($this->once())->method('save');

        $result = $this->updateObject(['id' => self::ELEMENT_IN_ALLOWED, 'parentId' => self::TARGET_FOLDER_ID]);

        $this->assertTrue($result['success']);
        $this->assertSame(self::TARGET_FOLDER_ID, $object->getParentId());
    }

    public function testUpdateObjectWithUnchangedParentNeedsNoCreateRight(): void
    {
        $this->restrictAllowedFolderToUpdate('object');

        $result = $this->updateObject(['id' => self::ELEMENT_IN_ALLOWED, 'parentId' => self::ALLOWED_FOLDER_ID]);

        $this->assertTrue($result['success']);
    }

    public function testUpdateUnknownObjectWithParentIsReported(): void
    {
        $result = $this->updateObject(['id' => -1, 'parentId' => self::ALLOWED_FOLDER_ID]);

        $this->assertFalse($result['success']);
        $this->assertSame('unable to update object. Unknown id or fullpath', $result['message']);
    }

    public function testCreateAssetInputParentCannotOverrideTheCheckedParent(): void
    {
        $result = $this->createAsset([
            'parentId' => self::TARGET_FOLDER_ID,
            'filename' => 'new.txt',
            'type' => 'text',
            'input' => ['parentId' => self::DENIED_FOLDER_ID],
        ]);

        $this->assertFalse($result['success']);
        $this->assertSame(self::CREATE_ASSET_DENIED, $result['message']);
    }

    public function testCreateAssetInputParentOutsideTheWorkspaceIsRejected(): void
    {
        $result = $this->createAsset([
            'parentId' => self::TARGET_FOLDER_ID,
            'filename' => 'new.txt',
            'type' => 'text',
            'input' => ['parentId' => self::OUTSIDE_FOLDER_ID],
        ]);

        $this->assertFalse($result['success']);
        $this->assertSame(self::CREATE_ASSET_DENIED, $result['message']);
    }

    public function testCreateAssetInDeniedParentIsStillRejected(): void
    {
        $result = $this->createAsset([
            'parentId' => self::DENIED_FOLDER_ID,
            'filename' => 'new.txt',
            'type' => 'text',
        ]);

        $this->assertFalse($result['success']);
        $this->assertSame(self::CREATE_ASSET_DENIED, $result['message']);
    }

    /**
     * @return array<string, mixed>
     */
    private function updateFolder(string $type, array $args, bool $omitPermissionCheck = false): array
    {
        $config = ['fields' => []];
        $this->mutationType($omitPermissionCheck)->buildUpdateFolderMutation(
            $type,
            $config,
            ['configuration' => $this->configuration()]
        );

        return $config['fields']['update' . ucfirst($type) . 'Folder']['resolve'](
            null,
            $args,
            ['configuration' => $this->configuration()],
            $this->createMock(ResolveInfo::class)
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function deleteFolder(string $type, array $args, bool $omitPermissionCheck = false): array
    {
        $config = ['fields' => []];
        $this->mutationType($omitPermissionCheck)->buildDeleteFolderMutation(
            $type,
            $config,
            ['configuration' => $this->configuration()]
        );

        return $config['fields']['delete' . ucfirst($type) . 'Folder']['resolve'](
            null,
            $args,
            ['configuration' => $this->configuration()],
            $this->createMock(ResolveInfo::class)
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function updateObject(array $args): array
    {
        $resolver = $this->mutationType(false)->getUpdateObjectResolver(
            [],
            $this->createMock(LocaleServiceInterface::class)
        );

        return $resolver(null, $args, [], $this->createMock(ResolveInfo::class));
    }

    /**
     * @return array<string, mixed>
     */
    private function createAsset(array $args): array
    {
        $config = ['fields' => []];
        $this->mutationType(false)->buildCreateAssetMutation($config, ['configuration' => $this->configuration()]);

        return $config['fields']['createAsset']['resolve'](null, $args, [], $this->createMock(ResolveInfo::class));
    }

    private function mutationType(bool $omitPermissionCheck): MutationType
    {
        // the service is final and needs the whole container: set just the lookups the mutation builders use
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

        return $mutationType;
    }

    private function configuration(): Configuration
    {
        return new Configuration('graphql', '/', self::CONFIG_NAME, [
            'schema' => ['specialEntities' => [
                'asset' => ['create' => true],
                'asset_folder' => ['update' => true, 'delete' => true],
                'object_folder' => ['update' => true, 'delete' => true],
                'document_folder' => ['update' => true, 'delete' => true],
            ]],
            'security' => ['skipPermissionCheck' => false],
        ]);
    }

    /**
     * The allowed folder keeps `update` but loses `create`, so only the unchanged-parent exception can let a save pass.
     */
    private function restrictAllowedFolderToUpdate(string $type): void
    {
        RuntimeCache::save(
            [self::ALLOWED_FOLDER_ID => $this->row(self::ALLOWED_FOLDER_ID, 0, 1)],
            'datahub_workspace_permissions_' . self::CONFIG_NAME . '_' . $type
        );
    }

    private function row(int $id, int $create, int $update, int $delete = 0): array
    {
        return [
            'cid' => $id,
            'cpath' => '/' . $id,
            'create' => $create,
            'read' => 1,
            'update' => $update,
            'delete' => $delete,
        ];
    }

    /**
     * An in-memory element that getById() finds in the runtime cache; saving is a no-op.
     *
     * @param class-string<ElementInterface> $class
     */
    private function element(
        string $type,
        string $class,
        int $id,
        int $parentId,
        ?ElementInterface $parent
    ): ElementInterface
    {
        $element = $this->getMockBuilder($class)->onlyMethods(['save', 'delete', 'getParent'])->getMock();
        $element->method('getParent')->willReturn($parent);
        $element->setId($id);
        $element->setParentId($parentId);
        $element->setPath('/');
        $type === 'asset' ? $element->setFilename('element-' . $id) : $element->setKey('element-' . $id);

        $key = $type . '_' . $id;
        $this->rememberCache($key);
        RuntimeCache::set($key, $element);
        $this->elements[$type . $id] = $element;

        return $element;
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
