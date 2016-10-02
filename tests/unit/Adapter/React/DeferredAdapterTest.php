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

require_once __DIR__ . '/../../Stub/PromiseStub.php';

use Tidal\WampWatch\Adapter\React\DeferredAdapter;
use Tidal\WampWatch\Test\Unit\Stub\PromiseStub;
use React\Promise\Deferred;
use React\Promise\Promise;
use PHPUnit_Framework_TestCase;

class DeferredAdapterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DeferredAdapter
     */
    private $adapter;

    /**
     * @var Deferred
     */
    private $adaptee;

    public function setUp()
    {
        $this->adapter = new DeferredAdapter(
            $this->getDeferredMock()
        );
        $this->adapter->setPromiseClass(PromiseStub::class);
    }

    public function test_constructor_sets_adaptee()
    {
        $this->assertEquals(
            $this->getDeferredMock(),
            $this->adapter->getAdaptee()
        );
    }

    public function test_can_access_promise_class()
    {
        $promiseClass = 'Foo';
        $this->adapter->setPromiseClass($promiseClass);

        $this->assertEquals(
            $promiseClass,
            $this->adapter->getPromiseClass()
        );
    }

    public function test_promise_returns_promise()
    {
        $this->getDeferredMock()
            ->expects($this->any())
            ->method('promise')
            ->willReturn(
                $this->getPromiseMock()
            );

        $this->assertInstanceOf(PromiseStub::class, $this->adapter->promise());
    }

    public function test_resolve_calls_adaptee()
    {
        $this->getDeferredMock()
            ->expects($this->once())
            ->method('resolve')
            ->willReturn(
                $this->getPromiseMock()
            );

        $this->adapter->resolve();
    }

    public function test_notifyresolve_calls_adaptee()
    {
        $this->getDeferredMock()
            ->expects($this->once())
            ->method('notify')
            ->willReturn(
                $this->getPromiseMock()
            );

        $this->adapter->notify();
    }

    public function test_reject_calls_adaptee()
    {
        $this->getDeferredMock()
            ->expects($this->once())
            ->method('reject')
            ->willReturn(
                $this->getPromiseMock()
            );

        $this->adapter->reject();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Deferred
     */
    private function getDeferredMock()
    {
        return $this->adaptee ?: $this->adaptee = $this->getMockBuilder(Deferred::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Promise
     */
    private function getPromiseMock()
    {
        return $this->getMockBuilder(Promise::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
