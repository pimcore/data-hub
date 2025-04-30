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

namespace Pimcore\Tests\Test;

use Codeception\Test\Unit;
use Pimcore\Tests\Support\Helper\DataType\Calculator;
use Pimcore\Tests\Support\ModelTester;

/**
 * @property ModelTester $tester
 */
abstract class ModelTestCase extends Unit
{
    protected function setUp(): void
    {
        parent::setUp();

        \Pimcore::getContainer()->set('test.calculatorservice', new Calculator());

        if ($this->needsDb()) {
            $this->setUpTestClasses();
        }
    }

    /**
     * Set up test classes before running tests
     */
    protected function setUpTestClasses()
    {
    }

    protected function needsDb()
    {
        return true;
    }
}
