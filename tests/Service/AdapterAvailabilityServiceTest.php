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

namespace Pimcore\Bundle\DataHubBundle\Service;

use Codeception\Test\Unit;

class AdapterAvailabilityServiceTest extends Unit
{
    public function testAnEnabledTypeIsReported(): void
    {
        $sut = new AdapterAvailabilityService(['graphql', 'simpleRest']);

        $this->assertTrue($sut->isEnabled('graphql'));
        $this->assertSame(['graphql', 'simpleRest'], $sut->getEnabledTypes());
    }

    public function testATypeOutsideTheEnabledListIsNotReported(): void
    {
        $sut = new AdapterAvailabilityService(['simpleRest']);

        $this->assertFalse($sut->isEnabled('graphql'));
        $this->assertTrue($sut->isEnabled('simpleRest'));
    }

    public function testNoTypeIsEnabledWithoutAnyAdapter(): void
    {
        $sut = new AdapterAvailabilityService([]);

        $this->assertFalse($sut->isEnabled('graphql'));
        $this->assertSame([], $sut->getEnabledTypes());
    }
}
