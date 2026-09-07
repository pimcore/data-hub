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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\Resolver;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\AssetType as AssetTypeResolver;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\Asset;
use Pimcore\Tests\Support\Util\TestHelper;

class AssetTypeFocalPointsTest extends Unit
{
    private AssetTypeResolver $resolver;

    private Asset\Image $image;

    private ?Asset\Video $video = null;

    private mixed $backupContext = null;

    protected function setUp(): void
    {
        $this->resolver = new AssetTypeResolver();

        if (RuntimeCache::isRegistered(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY)) {
            $this->backupContext = RuntimeCache::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        }
        $this->useConfigurationWithoutPermissionCheck();

        $this->image = TestHelper::createImageAsset('datahub-focalpoint-');
    }

    protected function tearDown(): void
    {
        if ($this->backupContext !== null) {
            RuntimeCache::set(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY, $this->backupContext);
        }

        $this->image->delete();
        $this->video?->delete();
    }

    public function testResolveFocalPointsReturnsCoordinatesWhenFocalPointIsSet(): void
    {
        $this->setFocalPoint(50.5, 25.0);

        $this->assertSame(
            ['x' => 50.5, 'y' => 25.0],
            $this->resolver->resolveFocalPoints(new ElementDescriptor($this->image))
        );
    }

    /**
     * Custom settings survive a round trip through the serialized asset data, so the
     * coordinates can come back as strings - the GraphQL field is a Float.
     */
    public function testResolveFocalPointsReturnsFloatsForStringCustomSettings(): void
    {
        $this->setFocalPoint('50.5', '25');

        $this->assertSame(
            ['x' => 50.5, 'y' => 25.0],
            $this->resolver->resolveFocalPoints(new ElementDescriptor($this->image))
        );
    }

    /**
     * Returning an array of nulls made graphql-php's default field resolver resolve every
     * requested subfield individually, so the response contained `focalPoints: {x: null, y: null}`
     * instead of `focalPoints: null`.
     */
    public function testResolveFocalPointsReturnsNullWhenNoFocalPointIsSet(): void
    {
        $this->assertNull($this->resolver->resolveFocalPoints(new ElementDescriptor($this->image)));
    }

    public function testResolveFocalPointsReturnsNullForNonImageAssets(): void
    {
        $this->video = TestHelper::createVideoAsset('datahub-focalpoint-');

        $this->assertNull($this->resolver->resolveFocalPoints(new ElementDescriptor($this->video)));
    }

    public function testResolveFocalPointsReturnsNullWithoutElement(): void
    {
        $this->assertNull($this->resolver->resolveFocalPoints());
    }

    private function setFocalPoint(mixed $x, mixed $y): void
    {
        $this->image->setCustomSetting('focalPointX', $x);
        $this->image->setCustomSetting('focalPointY', $y);
        $this->image->save();
    }

    private function useConfigurationWithoutPermissionCheck(): void
    {
        $configuration = new Configuration('graphql', '/1', 'datahub-focalpoint-test');
        $configuration->setConfiguration([
            'security' => ['skipPermissionCheck' => true],
        ]);

        RuntimeCache::set(
            PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY,
            ['clientname' => 'datahub-focalpoint-test', 'configuration' => $configuration]
        );
    }
}
