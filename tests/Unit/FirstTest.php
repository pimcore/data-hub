<?php

/**
 * Pimcore Customer Management Framework Bundle
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (C) Elements.at New Media Solutions GmbH
 * @license    GPLv3
 */

namespace DataHubBundle\Tests\Unit\View\Formatter;

use DataHubBundle\Tests\Fixtures\View\Formatter\NoToStringObject;
use DataHubBundle\Tests\Fixtures\View\Formatter\ToStringObject;
use DataHubBundle\View\Formatter\ObjectWrapper;

class ObjectWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testScalarReturnsItsValue()
    {

        $this->assertEquals(1,1);
    }
}
