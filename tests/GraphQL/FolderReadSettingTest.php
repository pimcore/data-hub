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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL;

use Codeception\Test\Unit;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\RelationsType;
use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\RelationType;
use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\RenderletType;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\PropertyType\ElementPropertyType;
use Pimcore\Bundle\DataHubBundle\GraphQL\RelationHelper;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Editable\Relation;
use Pimcore\Model\Document\Editable\Relations;
use Pimcore\Model\Document\Editable\Renderlet;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Property;

/**
 * The read settings of the object_folder and document_folder entities apply wherever a folder shows up:
 * a folder of a disabled type resolves to null (or is left out of a list), the same as a denied
 * workspace read, never to data or to a schema error.
 */
class FolderReadSettingTest extends Unit
{
    use ServiceStubTrait;

    private const OBJECT_FOLDER = 'object_folder';

    private const DOCUMENT_FOLDER = 'document_folder';

    private mixed $backupContext = null;

    protected function _before(): void
    {
        $this->extractDataCalls = [];
        $this->backupContext = RuntimeCache::isRegistered(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY)
            ? RuntimeCache::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY)
            : null;
        $this->resetSingletons();
    }

    protected function _after(): void
    {
        if ($this->backupContext !== null) {
            RuntimeCache::set(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY, $this->backupContext);
        } else {
            // do not leak the configuration of the last test into tests that expect no context
            RuntimeCache::getInstance()->offsetUnset(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        }
        $this->resetSingletons();
    }

    /**
     * @return array<string, array{0: class-string, 1: string, 2: string}>
     */
    public function folderProvider(): array
    {
        return [
            'object folder' => [DataObject\Folder::class, self::OBJECT_FOLDER, self::DOCUMENT_FOLDER],
            'document folder' => [Document\Folder::class, self::DOCUMENT_FOLDER, self::OBJECT_FOLDER],
        ];
    }

    /**
     * @dataProvider folderProvider
     */
    public function testFolderIsOnlyReadableIfItsReadSettingIsEnabled(
        string $folderClass,
        string $setting,
        string $otherSetting
    ): void {
        $folder = new $folderClass();

        $this->useConfiguration([]);
        $this->assertFalse(WorkspaceHelper::isFolderReadAllowed($folder));

        $this->useConfiguration([$setting => false]);
        $this->assertFalse(WorkspaceHelper::isFolderReadAllowed($folder));

        // the setting of the other folder type does not matter
        $this->useConfiguration([$setting => false, $otherSetting => true]);
        $this->assertFalse(WorkspaceHelper::isFolderReadAllowed($folder));

        $this->useConfiguration([$setting => true]);
        $this->assertTrue(WorkspaceHelper::isFolderReadAllowed($folder));
    }

    public function testElementsThatAreNoObjectOrDocumentFoldersAreNotAffected(): void
    {
        $this->useConfiguration([self::OBJECT_FOLDER => false, self::DOCUMENT_FOLDER => false]);

        $this->assertTrue(WorkspaceHelper::isFolderReadAllowed(new Document\Page()));
        $this->assertTrue(WorkspaceHelper::isFolderReadAllowed(new DataObject\Concrete()));
        $this->assertTrue(WorkspaceHelper::isFolderReadAllowed(new Asset\Folder()));
        $this->assertTrue(WorkspaceHelper::isFolderReadAllowed(null));
    }

    /**
     * @dataProvider folderProvider
     */
    public function testReadPermissionIsDeniedForDisabledFolderTypeEvenIfPermissionCheckIsSkipped(
        string $folderClass,
        string $setting
    ): void {
        $this->useConfiguration([$setting => false], true);

        $this->assertFalse(WorkspaceHelper::checkPermission(new $folderClass(), 'read'));
    }

    /**
     * @dataProvider folderProvider
     */
    public function testReadPermissionIsGrantedForEnabledFolderTypeIfPermissionCheckIsSkipped(
        string $folderClass,
        string $setting
    ): void {
        $this->useConfiguration([$setting => true], true);

        $this->assertTrue(WorkspaceHelper::checkPermission(new $folderClass(), 'read'));
    }

    /**
     * @dataProvider folderProvider
     */
    public function testReadSettingOnlyAppliesToReadAccess(string $folderClass, string $setting): void
    {
        $this->useConfiguration([$setting => false], true);

        $this->assertTrue(WorkspaceHelper::checkPermission(new $folderClass(), 'update'));
    }

    /**
     * @dataProvider folderProvider
     */
    public function testRelationOfDisabledFolderTypeResolvesToNull(string $folderClass, string $setting): void
    {
        $this->useConfiguration([$setting => false]);

        $this->assertNull($this->processRelation(new $folderClass()));
        $this->assertCount(0, $this->extractDataCalls, 'a disabled folder must not be hydrated');
    }

    /**
     * @dataProvider folderProvider
     */
    public function testRelationOfEnabledFolderTypeIsResolved(string $folderClass, string $setting): void
    {
        $this->useConfiguration([$setting => true]);

        $result = $this->processRelation(new $folderClass());

        $this->assertInstanceOf(ElementDescriptor::class, $result);
        $this->assertSame('folder', $result['__elementSubtype']);
        $this->assertCount(1, $this->extractDataCalls);
    }

    /**
     * @dataProvider folderProvider
     */
    public function testRelationEditableOfDisabledFolderTypeResolvesToNull(string $folderClass, string $setting): void
    {
        $this->useConfiguration([$setting => false]);

        $editable = $this->relationEditable(new $folderClass());

        $this->assertNull($this->resolveRelationField(RelationType::class, 'relation', $editable));
        $this->assertCount(0, $this->extractDataCalls);
    }

    /**
     * @dataProvider folderProvider
     */
    public function testRelationEditableOfEnabledFolderTypeIsResolved(string $folderClass, string $setting): void
    {
        $this->useConfiguration([$setting => true]);

        $editable = $this->relationEditable(new $folderClass());

        $result = $this->resolveRelationField(RelationType::class, 'relation', $editable);

        $this->assertInstanceOf(ElementDescriptor::class, $result);
        $this->assertCount(1, $this->extractDataCalls);
    }

    public function testRelationsEditableLeavesOutFoldersOfDisabledTypes(): void
    {
        $this->useConfiguration([self::OBJECT_FOLDER => false, self::DOCUMENT_FOLDER => true]);

        $page = new Document\Page();
        $relations = $this->createMock(Relations::class);
        $relations->method('getElements')->willReturn([new DataObject\Folder(), $page, new Document\Folder()]);

        $result = $this->resolveRelationField(RelationsType::class, 'relations', $relations);

        $this->assertCount(2, $result);
        $this->assertSame(['document', 'document'], array_column($result, '__elementType'));
        $this->assertSame(['page', 'folder'], array_column($result, '__elementSubtype'));
        $this->assertCount(2, $this->extractDataCalls);
    }

    /**
     * @dataProvider folderProvider
     */
    public function testRenderletOfDisabledFolderTypeResolvesToNull(string $folderClass, string $setting): void
    {
        $this->useConfiguration([$setting => false]);

        $renderlet = $this->createMock(Renderlet::class);
        $renderlet->method('getO')->willReturn(new $folderClass());

        $this->assertNull($this->resolveRelationField(RenderletType::class, 'relation', $renderlet));
        $this->assertCount(0, $this->extractDataCalls);
    }

    /**
     * @dataProvider propertyTypeProvider
     */
    public function testPropertyOfDisabledFolderTypeIsNotReadable(
        string $propertyType,
        string $folderClass,
        string $setting
    ): void {
        $this->useConfiguration([$setting => false]);

        $this->assertFalse(ElementPropertyType::isReadable($this->property($propertyType, new $folderClass())));
    }

    /**
     * @dataProvider propertyTypeProvider
     */
    public function testPropertyOfEnabledFolderTypeIsReadable(
        string $propertyType,
        string $folderClass,
        string $setting
    ): void {
        $this->useConfiguration([$setting => true]);

        $this->assertTrue(ElementPropertyType::isReadable($this->property($propertyType, new $folderClass())));
    }

    public function testPropertyOfNoFolderIsReadableWhateverTheReadSettings(): void
    {
        $this->useConfiguration([self::OBJECT_FOLDER => false, self::DOCUMENT_FOLDER => false]);

        $this->assertTrue(ElementPropertyType::isReadable($this->property('object', new DataObject\Concrete())));
        $this->assertTrue(ElementPropertyType::isReadable($this->property('document', new Document\Page())));
        $this->assertTrue(ElementPropertyType::isReadable($this->property('text', 'value')));
    }

    /**
     * @return array<string, array{0: string, 1: class-string, 2: string}>
     */
    public function propertyTypeProvider(): array
    {
        return [
            'object property' => ['object', DataObject\Folder::class, self::OBJECT_FOLDER],
            'document property' => ['document', Document\Folder::class, self::DOCUMENT_FOLDER],
        ];
    }

    /**
     * @param array<string, bool> $folderReadSettings
     */
    private function useConfiguration(array $folderReadSettings, bool $skipPermissionCheck = false): void
    {
        $specialEntities = [];
        foreach ($folderReadSettings as $entity => $enabled) {
            $specialEntities[$entity] = ['read' => $enabled];
        }

        $configuration = new Configuration('graphql', '/1', 'datahub-folder-read-test');
        $configuration->setConfiguration([
            'schema' => [
                'queryEntities' => [],
                'specialEntities' => $specialEntities,
            ],
            'security' => ['skipPermissionCheck' => $skipPermissionCheck],
        ]);

        RuntimeCache::set(
            PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY,
            ['clientname' => 'datahub-folder-read-test', 'configuration' => $configuration]
        );
    }

    private function relationEditable(DataObject\Folder|Document\Folder $target): Relation
    {
        $relation = $this->createMock(Relation::class);
        $relation->method('getElement')->willReturn($target);

        return $relation;
    }

    private function resolveRelationField(string $typeClass, string $field, mixed $value): mixed
    {
        $resolver = $typeClass::getInstance($this->createServiceStub())->getField($field)->resolveFn;

        return $resolver($value, [], [], null);
    }

    private function processRelation(ElementInterface $relation): ?ElementDescriptor
    {
        return RelationHelper::processRelation($relation, $this->createServiceStub(), [], [], $this->resolveInfo());
    }

    private function property(string $type, mixed $data): Property
    {
        $property = new Property();
        $property->setType($type);

        // Property::setData() only keeps the id of an element and loads it again on read
        \Closure::bind(static function (Property $property, mixed $data): void {
            $property->data = $data;
        }, null, Property::class)($property, $data);

        return $property;
    }

    private function resolveInfo(): ResolveInfo
    {
        return $this->createStub(ResolveInfo::class);
    }

    /**
     * The relation types cache their instance (and the Service passed to it) in a static property.
     */
    private function resetSingletons(): void
    {
        foreach ([RelationType::class, RelationsType::class, RenderletType::class] as $typeClass) {
            \Closure::bind(static function () use ($typeClass): void {
                $typeClass::$instance = null;
            }, null, $typeClass)();
        }
    }
}
