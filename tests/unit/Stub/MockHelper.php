<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\Unit\Stub;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class MockHelper
{
    /**
     * @param TestCase $test
     * @param  string  $class
     *
     * @return MockObject
     */
    public static function getNoConstructorMock(TestCase $test, $class)
    {
        return $test->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();
    }

}