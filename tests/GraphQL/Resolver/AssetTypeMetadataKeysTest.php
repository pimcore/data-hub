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
use Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\AssetType as AssetTypeResolver;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\Asset;
use Pimcore\Tests\Support\Util\TestHelper;

/**
 * The `keys` argument of an asset's `metadata` field.
 */
class AssetTypeMetadataKeysTest extends Unit
{
    private AssetTypeResolver $resolver;

    private Asset\Image $image;

    private bool $hadContext = false;

    private mixed $backupContext = null;

    protected function setUp(): void
    {
        $this->resolver = new AssetTypeResolver();

        if (RuntimeCache::isRegistered(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY)) {
            $this->hadContext = true;
            $this->backupContext = RuntimeCache::get(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        }
        $this->useConfigurationWithoutPermissionCheck();

        $this->image = TestHelper::createImageAsset('datahub-metadata-keys-');
        $this->image->addMetadata('author', 'input', 'Mike', '');
        $this->image->addMetadata('license', 'input', 'Pexels License', '');
        $this->image->addMetadata('title', 'input', 'Title', '');
        $this->image->addMetadata('title', 'input', 'Titel', 'de');
        $this->image->save();
    }

    protected function tearDown(): void
    {
        if ($this->hadContext) {
            RuntimeCache::set(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY, $this->backupContext);
        } elseif (RuntimeCache::isRegistered(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY)) {
            RuntimeCache::getInstance()->offsetUnset(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY);
        }

        $this->image->delete();
    }

    public function testWithoutKeysAllMetadataIsReturned(): void
    {
        $this->assertSame(
            ['author' => 'Mike', 'license' => 'Pexels License', 'title' => 'Titel'],
            $this->namesToData($this->resolve(['language' => 'de']))
        );
    }

    public function testKeysLimitTheMetadataToTheGivenNames(): void
    {
        $this->assertSame(
            ['license' => 'Pexels License', 'title' => 'Titel'],
            $this->namesToData($this->resolve(['language' => 'de', 'keys' => ['title', 'license']]))
        );
    }

    public function testKeysApplyWhenTheLanguageIsIgnored(): void
    {
        $result = $this->resolve(['ignore_language' => true, 'keys' => ['title']]);

        $this->assertSame(['title', 'title'], array_column($result, 'name'));
        $this->assertEqualsCanonicalizing(['', 'de'], array_column($result, 'language'));
    }

    public function testKeysThatMatchNothingReturnNull(): void
    {
        $this->assertNull($this->resolve(['language' => 'de', 'keys' => ['unknown']]));
    }

    /**
     * @param array<string, mixed> $args
     */
    private function resolve(array $args): ?array
    {
        return $this->resolver->resolveMetadata(new ElementDescriptor($this->image), $args);
    }

    /**
     * @param array<int, array<string, mixed>>|null $metadata
     *
     * @return array<string, mixed>
     */
    private function namesToData(?array $metadata): array
    {
        $result = array_column($metadata ?? [], 'data', 'name');
        ksort($result);

        return $result;
    }

    private function useConfigurationWithoutPermissionCheck(): void
    {
        $configuration = new Configuration('graphql', '/1', 'datahub-metadata-keys-test');
        $configuration->setConfiguration([
            'security' => ['skipPermissionCheck' => true],
        ]);

        RuntimeCache::set(
            PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY,
            ['clientname' => 'datahub-metadata-keys-test', 'configuration' => $configuration]
        );
    }
}
