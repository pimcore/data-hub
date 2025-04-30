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

namespace DataHubBundle\Tests\Test;

use Codeception\Test\Unit;
use Pimcore\Tests\Support\Util\TestHelper;

abstract class TestCase extends Unit
{
    /**
     * @var bool
     */
    protected $cleanupDbInSetup = true;

    protected $backupGlobalsExcludeList = ['IDE_EVAL_CACHE'];     // xdebug

    /**
     * Determine if the test needs a DB connection (will be skipped if no DB is present)
     *
     * @return bool
     */
    protected function needsDb()
    {
        return false;
    }

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->needsDb()) {
            TestHelper::checkDbSupport();

            // every single test assumes a clean database
            if ($this->cleanupDbInSetup) {
                TestHelper::cleanUp();
            }
        }

        \Pimcore::collectGarbage();
    }
}
