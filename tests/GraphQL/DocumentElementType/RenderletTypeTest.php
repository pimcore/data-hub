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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\DocumentElementType;

use Codeception\Test\Unit;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\RenderletType;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\AssetFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DataObjectFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DocumentFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Editable\Renderlet;
use Pimcore\Model\Factory;
use Pimcore\Translation\Translator;
use Psr\Container\ContainerInterface;

class RenderletTypeTest extends Unit
{
    private bool $originalHideUnpublishedObjects;

    private bool $originalHideUnpublishedDocuments;

    /**
     * Arguments of every Service::extractData() call made by the resolvers.
     */
    private array $extractDataCalls = [];

    protected function _before(): void
    {
        $this->originalHideUnpublishedObjects = AbstractObject::doHideUnpublished();
        $this->originalHideUnpublishedDocuments = Document::doHideUnpublished();
        $this->extractDataCalls = [];
        $this->resetSingleton();
    }

    protected function _after(): void
    {
        AbstractObject::setHideUnpublished($this->originalHideUnpublishedObjects);
        Document::setHideUnpublished($this->originalHideUnpublishedDocuments);
        $this->resetSingleton();
    }

    public function testTypeRegistersExpectedFields(): void
    {
        $type = RenderletType::getInstance($this->createServiceStub());

        $this->assertSame('document_editableRenderlet', $type->name);

        foreach (['_editableType', '_editableName', 'id', 'type', 'subtype', 'relation'] as $field) {
            $this->assertTrue($type->hasField($field), sprintf('field "%s" is missing', $field));
        }
    }

    /**
     * Regression test: getO() does not lazy-load and $o is stripped by
     * Renderlet::__sleep() when the document is serialized into the core cache,
     * so the resolver must call load() before reading the target. Without the
     * load() call, cache-hit documents resolved "relation" to null despite a
     * valid target.
     */
    public function testRelationResolverLoadsTargetBeforeReadingIt(): void
    {
        $target = $this->createAssetStub(11);

        $state = new \stdClass();
        $state->loaded = false;

        $renderlet = $this->createMock(Renderlet::class);
        $renderlet->expects($this->once())
            ->method('load')
            ->willReturnCallback(static function () use ($state): void {
                $state->loaded = true;
            });
        // simulate a cache-hit document: the target is only available after load()
        $renderlet->method('getO')
            ->willReturnCallback(static function () use ($state, $target) {
                return $state->loaded ? $target : null;
            });

        $result = $this->resolveField('relation', $renderlet);

        $this->assertInstanceOf(ElementDescriptor::class, $result);
        $this->assertSame(11, $result['id']);
        $this->assertSame('asset', $result['__elementType']);
        $this->assertCount(1, $this->extractDataCalls, 'Service::extractData() should be called exactly once');
    }

    /**
     * Regression test: getO() performs no publish check (unlike
     * Relation::getElement()), so the resolver must filter unpublished targets
     * itself instead of leaking them through the GraphQL API.
     */
    public function testRelationResolverHidesUnpublishedTarget(): void
    {
        AbstractObject::setHideUnpublished(true);

        $target = $this->createMock(Concrete::class);
        $target->method('isPublished')->willReturn(false);

        $renderlet = $this->createMock(Renderlet::class);
        $renderlet->method('getO')->willReturn($target);

        $this->assertNull($this->resolveField('relation', $renderlet));
        $this->assertCount(0, $this->extractDataCalls, 'unpublished targets must not be hydrated');
    }

    public function testRelationResolverReturnsPublishedTarget(): void
    {
        Document::setHideUnpublished(true);

        $target = $this->createMock(Document::class);
        $target->method('isPublished')->willReturn(true);
        $target->method('getId')->willReturn(42);
        $target->method('getType')->willReturn('page');

        $renderlet = $this->createMock(Renderlet::class);
        $renderlet->method('getO')->willReturn($target);

        $result = $this->resolveField('relation', $renderlet);

        $this->assertInstanceOf(ElementDescriptor::class, $result);
        $this->assertSame(42, $result['id']);
    }

    public function testRelationResolverReturnsNullWithoutTarget(): void
    {
        $renderlet = $this->createMock(Renderlet::class);
        $renderlet->method('getO')->willReturn(null);

        $this->assertNull($this->resolveField('relation', $renderlet));
    }

    /**
     * Regression test: "type" must expose the target's real element type
     * (asset/document/object). Renderlet::getType() is hardcoded to
     * 'renderlet' and would just duplicate "_editableType".
     */
    public function testTypeFieldExposesTargetElementType(): void
    {
        $renderlet = new Renderlet();
        $renderlet->setName('myRenderlet');
        $renderlet->setO($this->createAssetStub(7));

        $this->assertSame('asset', $this->resolveField('type', $renderlet));
        $this->assertSame('renderlet', $this->resolveField('_editableType', $renderlet));
        $this->assertSame('myRenderlet', $this->resolveField('_editableName', $renderlet));
    }

    public function testResolversIgnoreNonRenderletValues(): void
    {
        foreach (['_editableType', '_editableName', 'id', 'type', 'subtype', 'relation'] as $field) {
            $this->assertNull($this->resolveField($field, new \stdClass()), sprintf('field "%s"', $field));
        }
    }

    private function createAssetStub(int $id): Asset
    {
        $asset = $this->createMock(Asset::class);
        $asset->method('getId')->willReturn($id);
        $asset->method('getType')->willReturn('image');

        return $asset;
    }

    private function resolveField(string $field, mixed $value): mixed
    {
        $type = RenderletType::getInstance($this->createServiceStub());
        $resolver = $type->getField($field)->resolveFn;

        return $resolver($value, [], [], null);
    }

    /**
     * RenderletType caches its instance (and the Service passed to it) in a
     * static property, so it has to be cleared between tests.
     */
    private function resetSingleton(): void
    {
        \Closure::bind(static function (): void {
            self::$instance = null;
        }, null, RenderletType::class)();
    }

    /**
     * Builds a Service with stubbed dependencies, providing just what the
     * RenderletType resolvers use: buildGeneralType('anytarget') and
     * extractData() via the per-element field helpers.
     */
    private function createServiceStub(): Service
    {
        $anyTargetType = new ObjectType([
            'name' => 'renderlet_test_anytarget',
            'fields' => ['id' => Type::int()],
        ]);

        $factories = new class($anyTargetType) implements ContainerInterface {
            public function __construct(private readonly ObjectType $type)
            {
            }

            public function get(string $id): mixed
            {
                return new class($this->type) {
                    public function __construct(private readonly ObjectType $type)
                    {
                    }

                    public function build(): ObjectType
                    {
                        return $this->type;
                    }
                };
            }

            public function has(string $id): bool
            {
                return true;
            }
        };

        $fieldHelper = new class($this->extractDataCalls) {
            private array $calls;

            public function __construct(array &$calls)
            {
                $this->calls = &$calls;
            }

            public function extractData(...$args): void
            {
                $this->calls[] = $args;
            }
        };

        $service = new Service(
            new AssetFieldHelper(),
            new DocumentFieldHelper(),
            new DataObjectFieldHelper(),
            $this->createMock(LocaleServiceInterface::class),
            new Factory(),
            $this->createMock(Translator::class),
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
        );

        // swap in the recording field helper (class-scoped access, the
        // concrete helper classes are final and cannot be extended or mocked)
        \Closure::bind(static function (Service $service, object $fieldHelper): void {
            $service->assetFieldHelper = $fieldHelper;
            $service->documentFieldHelper = $fieldHelper;
            $service->objectFieldHelper = $fieldHelper;
        }, null, Service::class)($service, $fieldHelper);

        return $service;
    }
}
