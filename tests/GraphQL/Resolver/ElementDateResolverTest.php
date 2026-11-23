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
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\Element;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\Asset;

class ElementDateResolverTest extends Unit
{
    private ?Asset $asset = null;

    protected function _after(): void
    {
        $this->asset?->delete();
    }

    public function testDateResolversAcceptElementDescriptor(): void
    {
        $this->asset = Asset::create(1, [
            'filename' => 'datahub-element-date-resolver-test.txt',
            'data' => 'content',
        ]);

        $service = \Pimcore::getContainer()->get(Service::class);
        $resolver = new Element('asset', $service);
        $descriptor = new ElementDescriptor();
        $descriptor['id'] = $this->asset->getId();

        $this->assertNotNull($resolver->resolveCreationDate($descriptor));
        $this->assertNotNull($resolver->resolveModificationDate($descriptor));
    }

    public function testDateResolversReturnNullWithoutId(): void
    {
        $service = \Pimcore::getContainer()->get(Service::class);
        $resolver = new Element('asset', $service);

        $this->assertNull($resolver->resolveCreationDate(new ElementDescriptor()));
        $this->assertNull($resolver->resolveModificationDate([]));
    }
}
