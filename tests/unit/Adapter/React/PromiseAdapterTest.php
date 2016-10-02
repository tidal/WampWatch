<?php
/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Test\unit\Adapter\React;

use Tidal\WampWatch\Adapter\React\PromiseAdapter;
use React\Promise\Promise;
use PHPUnit_Framework_TestCase;

class PromiseAdapterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PromiseAdapter
     */
    private $adapter;

    /**
     * @var Promise
     */
    private $adaptee;

    public function setUp()
    {
        $this->adapter = new PromiseAdapter(
            $this->getPromiseMock()
        );
    }

    public function test_constructor_sets_adaptee()
    {
        $this->assertEquals(
            $this->getPromiseMock(),
            $this->adapter->getAdaptee()
        );
    }

    public function test_then_calls_adaptee()
    {
        $f = function () {
        };

        $this->getPromiseMock()
            ->expects($this->once())
            ->method('then')
            ->with($f, $f, $f)
            ->willReturn(
                $this->getPromiseMock()
            );

        $this->adapter->then($f, $f, $f);
    }

    public function test_done_calls_adaptee()
    {
        $f = function () {
        };

        $this->getPromiseMock()
            ->expects($this->once())
            ->method('done')
            ->with($f, $f, $f)
            ->willReturn(
                $this->getPromiseMock()
            );

        $this->adapter->done($f, $f, $f);
    }

    public function test_otherwise_calls_adaptee()
    {
        $f = function () {
        };

        $this->getPromiseMock()
            ->expects($this->once())
            ->method('otherwise')
            ->with($f)
            ->willReturn(
                $this->getPromiseMock()
            );

        $this->adapter->otherwise($f);
    }

    public function test_always_calls_adaptee()
    {
        $f = function () {
        };

        $this->getPromiseMock()
            ->expects($this->once())
            ->method('always')
            ->with($f)
            ->willReturn(
                $this->getPromiseMock()
            );

        $this->adapter->always($f);
    }

    public function test_progress_calls_adaptee()
    {
        $f = function () {
        };

        $this->getPromiseMock()
            ->expects($this->once())
            ->method('progress')
            ->with($f)
            ->willReturn(
                $this->getPromiseMock()
            );

        $this->adapter->progress($f);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Promise
     */
    private function getPromiseMock()
    {
        return $this->adaptee ?: $this->adaptee = $this->getMockBuilder(Promise::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
