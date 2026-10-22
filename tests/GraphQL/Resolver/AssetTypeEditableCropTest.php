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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\Resolver;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\AssetFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DataObjectFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DocumentFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\AssetType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Image\Thumbnail\Config;
use Pimcore\Model\Element\Service as ElementService;
use Pimcore\Model\Factory;
use Pimcore\Translation\Translator;
use Psr\Container\ContainerInterface;

/**
 * The crop a user selects in a document image editable has to end up in the thumbnail
 * of every image resolver, exactly like the frontend does it (cropPercent as first
 * item of the config, auto generated name).
 */
class AssetTypeEditableCropTest extends Unit
{
    private const ASSET_ID = 99997;

    private const CROP = ['width' => 50.0, 'height' => 40.0, 'top' => 10.0, 'left' => 20.0];

    private const MEDIA = '(max-width: 640px)';

    private AssetType $resolver;

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
        $image->setCustomSetting('imageWidth', 800);
        $image->setCustomSetting('imageHeight', 600);
        RuntimeCache::set(ElementService::getElementCacheTag('asset', self::ASSET_ID), $image);

        $this->resolver = new AssetType();
        $this->resolver->setGraphQLService($this->createService());
    }

    protected function _after(): void
    {
        $cache = RuntimeCache::getInstance();
        $cache->offsetUnset(ElementService::getElementCacheTag('asset', self::ASSET_ID));
        $cache->offsetUnset('imagethumb_' . crc32('editable_crop_test'));
        if ($this->hadContext) {
            RuntimeCache::set(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY, $this->backupContext);
        } else {
            $cache->offsetUnset(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        }
    }

    public function testFullpathWithThumbnailAppliesTheEditableCrop(): void
    {
        $thumbnail = $this->resolver->resolvePath($this->descriptor(true), ['thumbnail' => 'editable_crop_test']);

        $this->assertCropIsFirstItem($thumbnail->getConfig()->getItems());
        $this->assertStringStartsWith('editable_crop_test_auto_', $thumbnail->getConfig()->getName());
    }

    public function testFullpathWithoutCropKeepsTheThumbnailConfig(): void
    {
        $thumbnail = $this->resolver->resolvePath($this->descriptor(false), ['thumbnail' => 'editable_crop_test']);

        $this->assertSame('editable_crop_test', $thumbnail->getConfig()->getName());
        $this->assertSame('scaleByWidth', $thumbnail->getConfig()->getItems()[0]['method']);
    }

    public function testSrcSetMediaThumbnailsAreCropped(): void
    {
        $mediaQueries = $this->resolver->resolveSrcSet(
            $this->descriptor(true),
            ['thumbnail' => 'editable_crop_test', 'deferred' => false]
        );

        $this->assertCount(1, $mediaQueries);
        $this->assertSame(self::MEDIA, $mediaQueries[0]['descriptor']);
        $this->assertCropIsFirstItem($mediaQueries[0]['url']->getConfig()->getItems());
    }

    /**
     * Regression test: the media thumbnail of a cropped editable has an auto generated name that is
     * not a stored thumbnail config, so resolutions must not load it again by name.
     */
    public function testResolutionsOfASrcSetEntryUseTheGivenThumbnail(): void
    {
        $mediaQueries = $this->resolver->resolveSrcSet(
            $this->descriptor(true),
            ['thumbnail' => 'editable_crop_test', 'deferred' => false]
        );

        $resolutions = $this->resolver->resolveResolutions(
            $mediaQueries[0],
            ['types' => [1, 2]],
            []
        );

        $this->assertCount(2, $resolutions);
        $this->assertEquals(2, $resolutions[1]['resolution']);
        $this->assertCropIsFirstItem($resolutions[1]['url']->getConfig()->getItems());
        $this->assertEquals(2, $resolutions[1]['url']->getConfig()->getHighResolution());
    }

    /**
     * @return array<string, array{bool}>
     */
    public static function cropProvider(): array
    {
        return ['with crop' => [true], 'without crop' => [false]];
    }

    /**
     * @dataProvider cropProvider
     */
    public function testSrcSetEntriesAndTheirResolutionsKeepTheRequestedFormat(bool $withCrop): void
    {
        $mediaQueries = $this->resolver->resolveSrcSet(
            $this->descriptor($withCrop),
            ['thumbnail' => 'editable_crop_test', 'format' => 'webp', 'deferred' => false]
        );

        $this->assertCount(1, $mediaQueries);
        $this->assertSame('webp', $mediaQueries[0]['url']->getConfig()->getFormat());

        $resolutions = $this->resolver->resolveResolutions($mediaQueries[0], ['types' => [1, 2]], []);

        $this->assertCount(2, $resolutions);
        foreach ($resolutions as $resolution) {
            $this->assertSame('webp', $resolution['url']->getConfig()->getFormat());
        }
    }

    public function testResolutionsOfAnImageUseTheCrop(): void
    {
        $resolutions = $this->resolver->resolveResolutions(
            $this->descriptor(true),
            ['types' => [1, 2], 'thumbnail' => 'editable_crop_test'],
            []
        );

        $this->assertCount(2, $resolutions);
        $this->assertCropIsFirstItem($resolutions[1]['url']->getConfig()->getItems());
        $this->assertEquals(2, $resolutions[1]['url']->getConfig()->getHighResolution());
    }

    public function testMediaConfigsAreCroppedToo(): void
    {
        $thumbnail = $this->resolver->resolvePath($this->descriptor(true), ['thumbnail' => 'editable_crop_test']);

        $media = $thumbnail->getConfig()->getMedias()[self::MEDIA];
        $this->assertSame('cropPercent', $media[0]['method']);
        $this->assertSame('scaleByWidth', $media[1]['method']);
    }

    public function testDifferentCropsGiveDifferentThumbnailNames(): void
    {
        $other = $this->descriptor(true);
        $other['editableCrop']['width'] = 10.0;

        $a = $this->resolver->resolvePath($this->descriptor(true), ['thumbnail' => 'editable_crop_test']);
        $b = $this->resolver->resolvePath($other, ['thumbnail' => 'editable_crop_test']);

        $this->assertNotSame($a->getConfig()->getName(), $b->getConfig()->getName());
    }

    public function testStoredThumbnailConfigIsNotModified(): void
    {
        $this->resolver->resolvePath($this->descriptor(true), ['thumbnail' => 'editable_crop_test']);

        $stored = Config::getByName('editable_crop_test');
        $this->assertSame('editable_crop_test', $stored->getName());
        $this->assertCount(1, $stored->getItems());
    }

    private function assertCropIsFirstItem(array $items): void
    {
        $this->assertSame('cropPercent', $items[0]['method']);
        $this->assertEquals(
            ['width' => 50.0, 'height' => 40.0, 'y' => 10.0, 'x' => 20.0],
            $items[0]['arguments']
        );
        $this->assertSame('scaleByWidth', $items[1]['method']);
    }

    private function descriptor(bool $withCrop): ElementDescriptor
    {
        $config = new Config();
        $config->setName('editable_crop_test');
        $config->addItem('scaleByWidth', ['width' => 300]);
        $config->addItem('scaleByWidth', ['width' => 100], self::MEDIA);
        // getByName() reads its runtime cache before the database
        RuntimeCache::set('imagethumb_' . crc32('editable_crop_test'), $config);

        $descriptor = new ElementDescriptor(Asset::getById(self::ASSET_ID));
        if ($withCrop) {
            $descriptor['editableCrop'] = self::CROP;
        }

        return $descriptor;
    }

    private function createService(): Service
    {
        $container = $this->createMock(ContainerInterface::class);

        return new Service(
            new AssetFieldHelper(),
            new DocumentFieldHelper(),
            new DataObjectFieldHelper(),
            $this->createMock(LocaleServiceInterface::class),
            new Factory(),
            $this->createMock(Translator::class),
            $container,
            $container,
            $container,
            $container,
            $container,
            $container,
            $container,
            $container,
            $container,
            $container,
        );
    }
}
