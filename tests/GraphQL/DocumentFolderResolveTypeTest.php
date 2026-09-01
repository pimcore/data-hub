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
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\HrefType;
use Pimcore\Bundle\DataHubBundle\GraphQL\General\AnyDocumentTargetType;
use Pimcore\Bundle\DataHubBundle\GraphQL\General\AnyTargetType;
use Pimcore\Bundle\DataHubBundle\GraphQL\PropertyType\ObjectsType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyRelation;
use Pimcore\Model\Document;
use Pimcore\Tests\Support\Util\TestHelper;

/**
 * Relations, properties and hotspot metadata may point to a document folder. The
 * folder is resolved by the '_document_folder' type, all other documents by
 * their 'document_<type>' type.
 */
class DocumentFolderResolveTypeTest extends Unit
{
    private Service $service;

    private array $backupDocumentDataTypes = [];

    private ObjectType $folderType;

    private ObjectType $pageType;

    private Document\Folder $folder;

    private Document\Page $page;

    protected function setUp(): void
    {
        $this->folderType = new ObjectType(['name' => '_document_folder']);
        $this->pageType = new ObjectType(['name' => 'document_page']);

        $this->service = \Pimcore::getContainer()->get(Service::class);
        $this->backupDocumentDataTypes = $this->service->getDocumentDataTypes();
        $this->service->registerDocumentDataTypes([
            '_document_folder' => $this->folderType,
            'document_page' => $this->pageType,
        ]);

        $this->folder = TestHelper::createDocumentFolder('datahub-folder-');
        $this->page = TestHelper::createEmptyDocumentPage('datahub-page-');
    }

    protected function tearDown(): void
    {
        $this->service->registerDocumentDataTypes($this->backupDocumentDataTypes);

        $this->folder->delete();
        $this->page->delete();
    }

    public function testRelationResolvesDocumentFolder()
    {
        $relationType = $this->buildRelationType();
        $info = $this->resolveInfo();

        $this->assertSame(
            $this->folderType,
            $relationType->resolveType($this->folderElement(), [], $info)
        );
        $this->assertSame(
            $this->pageType,
            $relationType->resolveType($this->pageElement(), [], $info)
        );
    }

    public function testObjectPropertyResolvesDocumentFolder()
    {
        $propertyType = new ObjectsType($this->service);
        $info = $this->resolveInfo();

        $this->assertSame(
            $this->folderType,
            $propertyType->resolveType($this->folderElement(), [], $info)
        );
        $this->assertSame(
            $this->pageType,
            $propertyType->resolveType($this->pageElement(), [], $info)
        );
    }

    public function testAnyTargetResolvesDocumentFolder()
    {
        $anyTargetType = new AnyTargetType($this->service);
        $info = $this->resolveInfo();

        $this->assertSame(
            $this->folderType,
            $anyTargetType->resolveType($this->folderElement(), [], $info)
        );
        $this->assertSame(
            $this->pageType,
            $anyTargetType->resolveType($this->pageElement(), [], $info)
        );
    }

    public function testAnyDocumentTargetResolvesDocumentFolder()
    {
        $anyDocumentTargetType = new AnyDocumentTargetType($this->service);
        $info = $this->resolveInfo();

        // resolveType() of this union is declared as ?string,
        // so the returned type is cast to its name
        $this->assertSame(
            $this->folderType->name,
            $anyDocumentTargetType->resolveType($this->folderElement(), [], $info)
        );
        $this->assertSame(
            $this->pageType->name,
            $anyDocumentTargetType->resolveType($this->pageElement(), [], $info)
        );
    }

    private function buildRelationType(): HrefType
    {
        $fieldDefinition = new ManyToManyRelation();
        $fieldDefinition->setName('relation');

        $class = new ClassDefinition();
        $class->setName('unittest');

        return new HrefType($this->service, $fieldDefinition, $class);
    }

    private function folderElement(): array
    {
        return ['__elementType' => 'document', 'id' => $this->folder->getId()];
    }

    private function pageElement(): array
    {
        return ['__elementType' => 'document', 'id' => $this->page->getId()];
    }

    private function resolveInfo(): ResolveInfo
    {
        return $this->createStub(ResolveInfo::class);
    }
}
