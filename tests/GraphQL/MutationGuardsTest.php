<?php

declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL;

use Codeception\Test\Unit;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Folder as ObjectFolder;
use Pimcore\Model\Document;
use Pimcore\Model\Element\ElementInterface;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MutationGuardsTest extends Unit
{
    private const CLASS_NAME = 'MutGuardProduct';

    private ?ClassDefinition $class = null;

    protected function _after(): void
    {
        if ($this->class !== null) {
            $this->class->delete();
            $this->class = null;
        }
    }

    private function buildType(?ElementInterface $element): MutationGuardsStub
    {
        $type = (new ReflectionClass(MutationGuardsStub::class))->newInstanceWithoutConstructor();
        $type->element = $element;
        $type->setOmitPermissionCheck(true);

        return $type;
    }

    private function info(): ResolveInfo
    {
        return (new ReflectionClass(ResolveInfo::class))->newInstanceWithoutConstructor();
    }

    private function configuration(array $special, array $entityConfig = []): Configuration
    {
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getSpecialEntities')->willReturn($special);
        $configuration->method('getMutationEntities')->willReturn([self::CLASS_NAME]);
        $configuration->method('getMutationEntityConfig')->willReturn($entityConfig);

        return $configuration;
    }

    /**
     * @param class-string<ElementInterface> $className
     */
    private function element(string $className, int $deletes): ElementInterface
    {
        $element = $this->createMock($className);
        $element->expects($this->exactly($deletes))->method('delete');

        return $element;
    }

    private function resolve(MutationType $type, string $field, Configuration $configuration): array
    {
        $config = [];
        $context = ['configuration' => $configuration];
        $args = ['id' => 7];

        match ($field) {
            'deleteAsset' => $type->buildDeleteElementMutation($config, $context, 'asset'),
            'deleteDocument' => $type->buildDeleteElementMutation($config, $context, 'document'),
            'deleteAssetFolder' => $type->buildDeleteFolderMutation('asset', $config, $context),
            'deleteDocumentFolder' => $type->buildDeleteFolderMutation('document', $config, $context),
            'deleteObjectFolder' => $type->buildDeleteFolderMutation('object', $config, $context),
            'updateObjectFolder' => $type->buildUpdateFolderMutation('object', $config, $context),
            'updateAssetFolder' => $type->buildUpdateFolderMutation('asset', $config, $context),
            'updateDocumentFolder' => $type->buildUpdateFolderMutation('document', $config, $context),
            'deleteProduct' => $this->buildClassDelete($type, $config, $context),
        };

        $this->assertArrayHasKey($field, $config['fields']);

        return ($config['fields'][$field]['resolve'])(null, $args, $context, $this->info());
    }

    private function buildClassDelete(MutationType $type, array &$config, array $context): void
    {
        $this->class = new ClassDefinition();
        $this->class->setName(self::CLASS_NAME);
        $this->class->setId('mutguardproduct');
        $this->class->save();

        $property = (new ReflectionClass(MutationType::class))->getProperty('eventDispatcher');
        $property->setValue($type, $this->createMock(EventDispatcherInterface::class));
        $type->setGraphQLService($this->createMock(Service::class));

        $type->buildDataObjectMutations($config, $context);
        $config['fields']['deleteProduct'] = $config['fields']['delete' . self::CLASS_NAME];
    }

    private function concrete(string $classId, int $deletes): Concrete
    {
        $object = $this->createMock(Concrete::class);
        $object->method('getClassId')->willReturn($classId);
        $object->expects($this->exactly($deletes))->method('delete');

        return $object;
    }

    private function assertRefused(array $result): void
    {
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Unknown id or fullpath', $result['message']);
    }

    public function testDeleteClassRefusesObjectOfOtherClass(): void
    {
        $configuration = $this->configuration([], ['delete' => true]);
        $result = $this->resolve($this->buildType($this->concrete('other-class-id', 0)), 'deleteProduct', $configuration);
        $this->assertRefused($result);
    }

    public function testDeleteClassRefusesObjectFolder(): void
    {
        $configuration = $this->configuration([], ['delete' => true]);
        $result = $this->resolve($this->buildType($this->element(ObjectFolder::class, 0)), 'deleteProduct', $configuration);
        $this->assertRefused($result);
    }

    public function testDeleteClassStillDeletesObjectOfClass(): void
    {
        $configuration = $this->configuration([], ['delete' => true]);
        $type = $this->buildType(null);
        $config = [];
        $context = ['configuration' => $configuration];
        $this->buildClassDelete($type, $config, $context);
        $type->element = $this->concrete($this->class->getId(), 1);

        $result = ($config['fields']['deleteProduct']['resolve'])(null, ['id' => 7], $context, $this->info());
        $this->assertTrue($result['success']);
    }

    public function testDeleteClassStillDeletesVariantOfClass(): void
    {
        $configuration = $this->configuration([], ['delete' => true]);
        $type = $this->buildType(null);
        $config = [];
        $context = ['configuration' => $configuration];
        $this->buildClassDelete($type, $config, $context);
        $type->element = $this->concrete($this->class->getId(), 1);

        $result = ($config['fields']['deleteProduct']['resolve'])(null, ['id' => 7], $context, $this->info());
        $this->assertTrue($result['success']);
    }

    public function testUpdateAssetAndDocumentFoldersRefuseNonFolders(): void
    {
        $cases = [
            'updateAssetFolder' => [Asset\Image::class, 'asset_folder'],
            'updateDocumentFolder' => [Document\Page::class, 'document_folder'],
        ];
        foreach ($cases as $field => [$className, $entity]) {
            $element = $this->createMock($className);
            $element->expects($this->never())->method('save');
            $result = $this->resolve(
                $this->buildType($element),
                $field,
                $this->configuration([$entity => ['update' => true]])
            );
            $this->assertRefused($result);
        }
    }

    public function testUpdateAssetAndDocumentFoldersAcceptFolders(): void
    {
        $cases = [
            'updateAssetFolder' => [Asset\Folder::class, 'asset_folder'],
            'updateDocumentFolder' => [Document\Folder::class, 'document_folder'],
        ];
        foreach ($cases as $field => [$className, $entity]) {
            $element = $this->createMock($className);
            $element->expects($this->once())->method('save');
            $result = $this->resolve(
                $this->buildType($element),
                $field,
                $this->configuration([$entity => ['update' => true]])
            );
            $this->assertTrue($result['success'], $field);
        }
    }

    public function testDeleteAssetAndDocumentRefuseFolders(): void
    {
        $result = $this->resolve(
            $this->buildType($this->element(Asset\Folder::class, 0)),
            'deleteAsset',
            $this->configuration(['asset' => ['delete' => true]])
        );
        $this->assertRefused($result);

        $result = $this->resolve(
            $this->buildType($this->element(Document\Folder::class, 0)),
            'deleteDocument',
            $this->configuration(['document' => ['delete' => true]])
        );
        $this->assertRefused($result);
    }

    public function testDeleteAssetAndDocumentStillDeleteNonFolders(): void
    {
        $result = $this->resolve(
            $this->buildType($this->element(Asset\Image::class, 1)),
            'deleteAsset',
            $this->configuration(['asset' => ['delete' => true]])
        );
        $this->assertTrue($result['success']);

        $result = $this->resolve(
            $this->buildType($this->element(Document\Page::class, 1)),
            'deleteDocument',
            $this->configuration(['document' => ['delete' => true]])
        );
        $this->assertTrue($result['success']);
    }

    public function testDeleteFolderRefusesNonFolders(): void
    {
        $cases = [
            'deleteAssetFolder' => [Asset\Image::class, 'asset_folder'],
            'deleteDocumentFolder' => [Document\Page::class, 'document_folder'],
            'deleteObjectFolder' => [Concrete::class, 'object_folder'],
        ];
        foreach ($cases as $field => [$className, $entity]) {
            $result = $this->resolve(
                $this->buildType($this->element($className, 0)),
                $field,
                $this->configuration([$entity => ['delete' => true]])
            );
            $this->assertRefused($result);
        }
    }

    public function testDeleteFolderStillDeletesFolders(): void
    {
        $cases = [
            'deleteAssetFolder' => [Asset\Folder::class, 'asset_folder'],
            'deleteDocumentFolder' => [Document\Folder::class, 'document_folder'],
            'deleteObjectFolder' => [ObjectFolder::class, 'object_folder'],
        ];
        foreach ($cases as $field => [$className, $entity]) {
            $result = $this->resolve(
                $this->buildType($this->element($className, 1)),
                $field,
                $this->configuration([$entity => ['delete' => true]])
            );
            $this->assertTrue($result['success'], $field);
        }
    }

    public function testUpdateFolderRefusesNonFolderAndAcceptsFolder(): void
    {
        $configuration = $this->configuration(['object_folder' => ['update' => true]]);

        $object = $this->createMock(Concrete::class);
        $object->expects($this->never())->method('save');
        $this->assertRefused($this->resolve($this->buildType($object), 'updateObjectFolder', $configuration));

        $folder = $this->createMock(ObjectFolder::class);
        $folder->expects($this->once())->method('save');
        $this->assertTrue($this->resolve($this->buildType($folder), 'updateObjectFolder', $configuration)['success']);
    }
}

/**
 * @internal
 */
class MutationGuardsStub extends MutationType
{
    public ?ElementInterface $element = null;

    protected function getElementById($type, $id)
    {
        return $this->element;
    }

    protected function getElementByPath($type, $fullpath)
    {
        return $this->element;
    }
}
