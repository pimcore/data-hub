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

namespace Pimcore\Bundle\DataHubBundle\Tests\Model;

use function array_map;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Configuration\Dao;
use Pimcore\Bundle\DataHubBundle\Proposal\StoredConfigurations;
use Pimcore\Tests\Support\Test\ModelTestCase;
use ReflectionProperty;

class StoredConfigurationsTest extends ModelTestCase
{
    private const string NAME = 'stored-configurations-lookup';

    protected function setUp(): void
    {
        parent::setUp();
        $this->forgetListedConfigurations();
    }

    protected function tearDown(): void
    {
        Configuration::getByName(self::NAME)?->delete();
        $this->forgetListedConfigurations();

        parent::tearDown();
    }

    public function testAnUnknownNameIsNotFound(): void
    {
        $this->assertNull((new StoredConfigurations())->find('stored-configurations-missing'));
    }

    public function testAStoredConfigurationIsFoundByNameAndListed(): void
    {
        $configuration = new Configuration('graphql', null, self::NAME);
        $configuration->setConfiguration(['general' => ['active' => false, 'description' => 'lookup']]);
        $configuration->save();

        $stored = new StoredConfigurations();
        $found = $stored->find(self::NAME);

        $this->assertNotNull($found);
        $this->assertSame('graphql', $found->getType());
        $this->assertSame('lookup', $found->getConfiguration()['general']['description']);
        $this->assertContains(
            self::NAME,
            array_map(static fn (Configuration $listed): ?string => $listed->getName(), $stored->all()),
        );
    }

    /** the list is cached per process, and neither a save nor a delete resets it */
    private function forgetListedConfigurations(): void
    {
        (new ReflectionProperty(Dao::class, '_config'))->setValue(null, null);
    }
}
