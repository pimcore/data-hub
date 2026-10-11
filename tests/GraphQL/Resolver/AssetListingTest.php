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
use Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\AssetListing;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\Asset;
use Symfony\Component\EventDispatcher\EventDispatcher;

class AssetListingTest extends Unit
{
    private Service $service;

    private array $assetIds = [];

    private array $context;

    protected function setUp(): void
    {
        $this->service = \Pimcore::getContainer()->get(Service::class);

        foreach (['one', 'two', 'three'] as $name) {
            $asset = Asset::create(1, [
                'filename' => 'datahub-asset-listing-' . $name . '.txt',
                'data' => 'content-' . $name,
            ]);
            $this->assetIds[] = $asset->getId();
        }

        $configuration = new Configuration('graphql', '/datahub-asset-listing-test', 'datahub-asset-listing-test', [
            'security' => [
                'skipPermissionCheck' => true,
            ],
        ]);

        $this->context = [
            'clientname' => $configuration->getName(),
            'configuration' => $configuration,
        ];

        RuntimeCache::set(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY, $this->context);
    }

    protected function tearDown(): void
    {
        foreach ($this->assetIds as $id) {
            Asset::getById($id)?->delete();
        }
    }

    public function testResolveListingWithCommaSeparatedIdsReturnsOnlyRequestedAssets(): void
    {
        $assetListing = new AssetListing($this->service, new EventDispatcher());

        $requestedIds = array_slice($this->assetIds, 0, 2);
        $result = $assetListing->resolveListing([], ['ids' => implode(',', $requestedIds)], $this->context);

        $returnedIds = array_map(static fn (array $edge) => $edge['node']->getId(), $result['edges']);
        sort($returnedIds);
        sort($requestedIds);

        $this->assertSame($requestedIds, $returnedIds);
    }

    public function testResolveListingWithSqlInjectionPayloadInIdsDoesNotBypassIdFilter(): void
    {
        $assetListing = new AssetListing($this->service, new EventDispatcher());

        $result = $assetListing->resolveListing([], ['ids' => '0)) OR 1=1 -- '], $this->context);

        $this->assertSame(0, $result['totalCount']);
        $this->assertCount(0, $result['edges']);
    }
}
