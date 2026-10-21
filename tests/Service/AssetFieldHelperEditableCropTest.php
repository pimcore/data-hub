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
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\AssetFieldHelper;
use Pimcore\Model\Asset\Image\Thumbnail\Config;

/**
 * The crop a user selects in a document image editable has to end up in the thumbnail
 * config, exactly like the frontend does it (cropPercent as first item, auto name).
 */
class AssetFieldHelperEditableCropTest extends Unit
{
    private const CROP = ['width' => 50.0, 'height' => 40.0, 'top' => 10.0, 'left' => 20.0];

    private function createConfig(): Config
    {
        $config = new Config();
        $config->setName('pme_crop_test');
        $config->addItem('scaleByWidth', ['width' => 300]);

        return $config;
    }

    private function createHelper(): AssetFieldHelper
    {
        return (new \ReflectionClass(AssetFieldHelper::class))->newInstanceWithoutConstructor();
    }

    public function testCropIsAddedAsFirstItemWithAutoName(): void
    {
        $original = $this->createConfig();

        $cropped = $this->createHelper()->applyEditableCrop($original, self::CROP);

        $items = $cropped->getItems();
        $this->assertSame('cropPercent', $items[0]['method']);
        $this->assertEquals(
            ['width' => 50.0, 'height' => 40.0, 'y' => 10.0, 'x' => 20.0],
            $items[0]['arguments']
        );
        $this->assertSame('scaleByWidth', $items[1]['method']);
        $this->assertStringStartsWith('pme_crop_test_auto_', $cropped->getName());
    }

    public function testOriginalConfigIsNotModified(): void
    {
        $original = $this->createConfig();

        $this->createHelper()->applyEditableCrop($original, self::CROP);

        $this->assertSame('pme_crop_test', $original->getName());
        $this->assertCount(1, $original->getItems());
    }

    public function testMediaQueriesAreCroppedToo(): void
    {
        $original = $this->createConfig();
        $original->addItem('scaleByWidth', ['width' => 100], '(max-width: 640px)');

        $cropped = $this->createHelper()->applyEditableCrop($original, self::CROP);

        $media = $cropped->getMedias()['(max-width: 640px)'];
        $this->assertSame('cropPercent', $media[0]['method']);
        $this->assertSame('scaleByWidth', $media[1]['method']);
    }

    public function testDifferentCropsGiveDifferentNames(): void
    {
        $helper = $this->createHelper();

        $a = $helper->applyEditableCrop($this->createConfig(), self::CROP);
        $b = $helper->applyEditableCrop($this->createConfig(), ['width' => 10.0] + self::CROP);

        $this->assertNotSame($a->getName(), $b->getName());
    }
}
