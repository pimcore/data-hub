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
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\ImageType;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\AssetFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DataObjectFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DocumentFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\Asset;
use Pimcore\Model\Document\Editable\Image;
use Pimcore\Model\Element\Service as ElementService;
use Pimcore\Model\Factory;
use Pimcore\Translation\Translator;
use Psr\Container\ContainerInterface;

/**
 * The image resolver of the document image editable has to hand the crop the user selected
 * on to the asset resolvers (as "editableCrop"), and only when there is one.
 */
class ImageTypeEditableCropTest extends Unit
{
    private const ASSET_ID = 99996;

    private mixed $backupContext = null;

    private bool $hadContext = false;

    protected function _before(): void
    {
        $this->hadContext = RuntimeCache::isRegistered(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        if ($this->hadContext) {
            $this->backupContext = RuntimeCache::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        }

        RuntimeCache::set(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY, [
            'clientname' => 'editable-crop-test',
            'configuration' => new Configuration('graphql', '/editable-crop-test', 'editable-crop-test', [
                'security' => ['skipPermissionCheck' => true],
            ]),
        ]);

        $image = new Asset\Image();
        $image->setId(self::ASSET_ID);
        $image->setFilename('editable-crop-test.jpg');
        RuntimeCache::set(ElementService::getElementCacheTag('asset', self::ASSET_ID), $image);

        $this->resetSingleton();
    }

    protected function _after(): void
    {
        $cache = RuntimeCache::getInstance();
        $cache->offsetUnset(ElementService::getElementCacheTag('asset', self::ASSET_ID));
        if ($this->hadContext) {
            RuntimeCache::set(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY, $this->backupContext);
        } else {
            $cache->offsetUnset(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        }

        $this->resetSingleton();
    }

    public function testImageCarriesTheCropTheUserSelected(): void
    {
        $editable = $this->editable()
            ->setCropPercent(true)
            ->setCropWidth(50.0)
            ->setCropHeight(40.0)
            ->setCropTop(10.0)
            ->setCropLeft(20.0);

        $image = $this->resolveImage($editable);

        $this->assertInstanceOf(ElementDescriptor::class, $image);
        $this->assertSame(
            ['width' => 50.0, 'height' => 40.0, 'top' => 10.0, 'left' => 20.0],
            $image['editableCrop']
        );
    }

    public function testImageWithoutCropPercentCarriesNoCrop(): void
    {
        // crop values without the percent flag are not a selection of the user (core ignores them as well)
        $editable = $this->editable()->setCropWidth(50.0)->setCropHeight(40.0);

        $image = $this->resolveImage($editable);

        $this->assertInstanceOf(ElementDescriptor::class, $image);
        $this->assertArrayNotHasKey('editableCrop', $image->getArrayCopy());
    }

    public function testEditableWithoutAssetResolvesToNull(): void
    {
        $this->assertNull($this->resolveImage(new Image()));
    }

    private function editable(): Image
    {
        $editable = new Image();
        $editable->setId(self::ASSET_ID);

        return $editable;
    }

    private function resolveImage(Image $editable): mixed
    {
        $type = ImageType::getInstance($this->createService());
        $resolve = $type->getField('image')->resolveFn;

        return $resolve($editable, [], [], null);
    }

    private function resetSingleton(): void
    {
        \Closure::bind(static function (): void {
            ImageType::$instance = null;
        }, null, ImageType::class)();
    }

    private function createService(): Service
    {
        $anyType = new ObjectType(['name' => 'image_test_type', 'fields' => ['id' => Type::int()]]);

        $factories = new class($anyType) implements ContainerInterface {
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

        // the resolver only needs extractData() to be harmless (the concrete helper classes are final)
        $fieldHelper = new class() {
            public function extractData(...$args): void
            {
            }
        };
        \Closure::bind(static function (Service $service, object $fieldHelper): void {
            $service->assetFieldHelper = $fieldHelper;
        }, null, Service::class)($service, $fieldHelper);

        return $service;
    }
}
