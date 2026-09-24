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
use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType\DocumentType;
use Pimcore\Bundle\DataHubBundle\GraphQL\General\AnyDocumentTargetType;
use Pimcore\Bundle\DataHubBundle\GraphQL\General\AnyTargetType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\Document;
use Pimcore\Tests\Support\Util\TestHelper;

/**
 * getDocument() on a folder path resolves to the '_document_folder' type, so the
 * 'document' union has to contain that type as a possible type. Unions composing
 * the 'document' union must not list it twice.
 */
class DocumentUnionFolderTest extends Unit
{
    // '_document_folder' is only the key the type is registered under; the
    // GraphQL type it holds is named 'document_folder'
    private const FOLDER_TYPE = 'document_folder';

    private const PAGE_TYPE = 'document_page';

    private Service $service;

    private DocumentType $documentUnionType;

    private Document\Folder $folder;

    private Document\Page $page;

    private mixed $backupContext = null;

    protected function setUp(): void
    {
        $this->service = \Pimcore::getContainer()->get(Service::class);

        $this->documentUnionType = $this->service->getDocumentTypeDefinition('document');

        if (RuntimeCache::isRegistered(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY)) {
            $this->backupContext = RuntimeCache::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        }

        $this->folder = TestHelper::createDocumentFolder('datahub-folder-');
        $this->page = TestHelper::createEmptyDocumentPage('datahub-page-');
    }

    protected function tearDown(): void
    {
        if ($this->backupContext !== null) {
            RuntimeCache::set(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY, $this->backupContext);
        }

        $this->folder->delete();
        $this->page->delete();
    }

    public function testDocumentUnionContainsFolderType()
    {
        $types = $this->documentUnionType->getTypes();

        $this->assertSame(1, $this->countType($types, self::FOLDER_TYPE));
        $this->assertSame(1, $this->countType($types, self::PAGE_TYPE));
    }

    public function testDocumentUnionResolvesFolderAndPage()
    {
        $info = $this->createStub(ResolveInfo::class);

        // without the folder branch resolveType() returns null and the union
        // fails with "Abstract type Document must resolve to an Object type"
        $this->assertSame(
            self::FOLDER_TYPE,
            $this->documentUnionType->resolveType($this->element($this->folder), [], $info)?->name
        );
        $this->assertSame(
            self::PAGE_TYPE,
            $this->documentUnionType->resolveType($this->element($this->page), [], $info)?->name
        );
    }

    public function testAnyDocumentTargetContainsFolderTypeOnce()
    {
        $types = (new AnyDocumentTargetType($this->service))->getTypes();

        $this->assertSame(1, $this->countType($types, self::FOLDER_TYPE));
    }

    public function testAnyTargetContainsFolderTypeOnceWhenDocumentUnionIsEnabled()
    {
        $this->useConfiguration(['document' => true, 'document_folder' => true]);

        $types = (new AnyTargetType($this->service))->getTypes();

        $this->assertSame(1, $this->countType($types, self::FOLDER_TYPE));
    }

    public function testAnyTargetContainsFolderTypeWhenOnlyFolderIsEnabled()
    {
        $this->useConfiguration(['document' => false, 'document_folder' => true]);

        $types = (new AnyTargetType($this->service))->getTypes();

        $this->assertSame(1, $this->countType($types, self::FOLDER_TYPE));
    }

    /**
     * The union members are compared by name: the container inlines the type
     * services, so the same GraphQL type can be a different PHP instance.
     */
    private function countType(array $types, string $name): int
    {
        return count(array_filter($types, static fn ($candidate) => $candidate->name === $name));
    }

    /**
     * @param array<string, bool> $enabledSpecialEntities
     */
    private function useConfiguration(array $enabledSpecialEntities): void
    {
        $specialEntities = [];
        foreach ($enabledSpecialEntities as $entity => $enabled) {
            $specialEntities[$entity] = ['read' => $enabled];
        }

        $configuration = new Configuration('graphql', '/1', 'datahub-union-test');
        $configuration->setConfiguration([
            'schema' => [
                'queryEntities' => [],
                'specialEntities' => $specialEntities,
            ],
        ]);

        RuntimeCache::set(
            PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY,
            ['clientname' => 'datahub-union-test', 'configuration' => $configuration]
        );
    }

    private function element(Document $document): array
    {
        return ['__elementType' => 'document', 'id' => $document->getId()];
    }
}
