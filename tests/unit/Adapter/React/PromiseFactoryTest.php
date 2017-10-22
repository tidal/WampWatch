<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\Unit\Adapter\React;

use Tidal\WampWatch\Adapter\React\PromiseFactory;
use Tidal\WampWatch\Adapter\React\PromiseAdapter;
use Tidal\WampWatch\Adapter\React\DeferredAdapter;
use React\Promise\Promise as ReactPromise;
use React\Promise\Deferred as ReactDeferred;

class PromiseFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PromiseFactory
     */
    private $factory;

    /**
     * @var callable empty function
     */
    private $f;

    public function setUp()
    {
        $this->factory = new PromiseFactory();
        $this->f = function () {
        };
    }

    public function test_can_create()
    {
        $this->assertInstanceOf(
            PromiseAdapter::class,
            $this->factory->create($this->f)
        );
    }

    public function test_create_wraps_react_promise()
    {
        $this->assertInstanceOf(
            ReactPromise::class,
            $this->factory->create($this->f)->getAdaptee()
        );
    }

    public function test_can_create_from_adaptee()
    {
        $this->assertInstanceOf(
            PromiseAdapter::class,
            $this->factory->createFromAdaptee(
                $this->createReactPromiseMock()
            )
        );
    }

    public function test_all_accepts_array_of_promises()
    {
        $this->assertInstanceOf(
            PromiseAdapter::class,
            $this->factory->all([
                $this->createPromiseMock(),
                $this->createPromiseMock()
            ])
        );
    }

    public function test_all_accepts_array_of_react_promises()
    {
        $this->assertInstanceOf(
            PromiseAdapter::class,
            $this->factory->all([
                $this->createReactPromiseMock(),
                $this->createReactPromiseMock()
            ])
        );
    }

    public function test_all_throws_exception_on_array_of_non_promises()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class
        );

        $this->factory->all([
            new \stdClass(),
            new \stdClass()
        ]);
    }

    public function test_all_is_not_done_when_not_all_promises_are_done()
    {
        $promiseCount = 2;

        $done = false;
        /** @var DeferredAdapter[] $deferredPromises */
        $deferredPromises = [];
        /** @var PromiseAdapter[] $promises */
        $promises = [];

        for ($x = 0; $x < $promiseCount; $x++) {
            $deferredPromises[] = $deferred = $this->createDeferredMock();
            $promises[] = $deferred->promise();
        }

        $this->factory
            ->all($promises)
            ->done(
                function () use (&$done) {
                    $done = true;
                }
            );

        $deferredPromises[0]->resolve('foo');

        $this->assertFalse($done);
    }

    public function test_all_is_done_when_all_promises_are_done()
    {
        $promiseCount = 4;

        $done = false;
        /** @var DeferredAdapter[] $deferredPromises */
        $deferredPromises = [];
        /** @var PromiseAdapter[] $promises */
        $promises = [];

        for ($x = 0; $x < $promiseCount; $x++) {
            $deferredPromises[] = $deferred = $this->createDeferredMock(true);
            $promises[] = $deferred->promise();
        }

        $this->factory
            ->all($promises)
            ->done(
                function () use (&$done) {
                    $done = true;
                }
            );

        foreach ($deferredPromises as $deferred) {
            $deferred->resolve('foo');
        }

        $this->assertTrue($done);
    }

    public function test_all_returns_array_of_all_results()
    {
        $promiseCount = 4;

        $results = null;
        /** @var DeferredAdapter[] $deferredPromises */
        $deferredPromises = [];
        /** @var PromiseAdapter[] $promises */
        $promises = [];

        for ($x = 0; $x < $promiseCount; $x++) {
            $deferredPromises[] = $deferred = $this->createDeferredMock(true);
            $promises[] = $deferred->promise();
        }

        $this->factory
            ->all($promises)
            ->done(
                function ($res) use (&$results) {
                    $results = $res;
                }
            );

        foreach ($deferredPromises as $key => $deferred) {
            $deferred->resolve($key);
        }

        $this->assertEquals([0, 1, 2, 3], $results);
    }

    public function test_any_is_done_with_any_result()
    {
        $promiseCount = 2;

        $done = false;
        /** @var DeferredAdapter[] $deferredPromises */
        $deferredPromises = [];
        /** @var PromiseAdapter[] $promises */
        $promises = [];

        for ($x = 0; $x < $promiseCount; $x++) {
            $deferredPromises[] = $deferred = $this->createDeferredMock();
            $promises[] = $deferred->promise();
        }

        $this->factory
            ->any($promises)
            ->done(
                function ($res) use (&$done) {
                    var_dump($res);
                    $done = true;
                }
            );

        $deferredPromises[0]->resolve('foo');

        $this->assertTrue($done);
    }

    public function test_any_returns_result_of_first_result()
    {
        $promiseCount = 2;

        $result = null;
        /** @var DeferredAdapter[] $deferredPromises */
        $deferredPromises = [];
        /** @var PromiseAdapter[] $promises */
        $promises = [];

        for ($x = 0; $x < $promiseCount; $x++) {
            $deferredPromises[] = $deferred = $this->createDeferredMock();
            $promises[] = $deferred->promise();
        }

        $this->factory
            ->any($promises)
            ->done(
                function ($res) use (&$result) {
                    $result = $res;
                }
            );

        $deferredPromises[0]->resolve('foo');

        $this->assertEquals('foo', $result);
    }

    public function test_some_is_done_when_some_promises_are_done()
    {
        $promiseCount = 4;
        $someCount = 2;

        $done = false;
        /** @var DeferredAdapter[] $deferredPromises */
        $deferredPromises = [];
        /** @var PromiseAdapter[] $promises */
        $promises = [];

        for ($x = 0; $x < $promiseCount; $x++) {
            $deferredPromises[] = $deferred = $this->createDeferredMock($x < $someCount);
            $promises[] = $deferred->promise();
        }

        $this->factory
            ->some($promises, $someCount)
            ->done(
                function () use (&$done) {
                    $done = true;
                }
            );

        for ($x = 0; $x < $someCount; $x++) {
            $deferredPromises[$x]->resolve('foo');
        }

        $this->assertTrue($done);
    }

    public function test_some_returns_results_of_some_results()
    {
        $promiseCount = 4;
        $someCount = 2;

        $result = null;
        /** @var DeferredAdapter[] $deferredPromises */
        $deferredPromises = [];
        /** @var PromiseAdapter[] $promises */
        $promises = [];

        for ($x = 0; $x < $promiseCount; $x++) {
            $deferredPromises[] = $deferred = $this->createDeferredMock($x < $someCount);
            $promises[] = $deferred->promise();
        }

        $this->factory
            ->some($promises, $someCount)
            ->done(
                function ($res) use (&$result) {
                    $result = $res;
                }
            );

        for ($x = 0; $x < $someCount; $x++) {
            $deferredPromises[$x]->resolve($x);
        }

        $this->assertEquals([0, 1], $result);
    }

    /**
     * @param bool $expectResolve whether the mock should expect DeferredAdapter::resolve() to be called
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|DeferredAdapter
     *
     * @see DeferredAdapter::resolve()
     */
    private function createDeferredMock($expectResolve = false)
    {
        $resolve = null;
        $reject = null;
        $notify = null;

        /** @var \PHPUnit_Framework_MockObject_MockObject|DeferredAdapter $mock */
        $mock = $this->getMockBuilder(DeferredAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $adaptee = $this->createReactDeferredMock($expectResolve);
        $promise = $adaptee->promise();

        $mock->expects($this->any())
            ->method('getAdaptee')
            ->willReturn(
                $adaptee
            );

        $mock->expects($this->any())
            ->method('resolve')
            ->with(
                $this->anything()
            )->willReturnCallback(
                function ($value = null) use ($adaptee) {
                    $adaptee->resolve($value);
                }
            );

        $mock->expects($this->any())
            ->method('promise')
            ->willReturn(
                $promise
            );


        return $mock;
    }

    /**
     * @param bool $expectResolve
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|ReactDeferred
     */
    private function createReactDeferredMock($expectResolve = false)
    {
        $resolve = null;
        $reject = null;
        $notify = null;

        $mock = $this->getMockBuilder(ReactDeferred::class)
            ->disableOriginalConstructor()
            ->getMock();

        $promise = $this->createReactPromiseMock();

        $mock->expects($this->any())
            ->method('promise')
            ->willReturn(
                $promise
            );

        $promise->expects($this->any())
            ->method('then')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->anything()
            )->willReturn($promise);

        $promise->expects($this->any())
            ->method('done')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->anything()
            )->willReturnCallback(
                function ($res = null, $rej = null, $not = null) use ($promise, &$resolve, &$reject, &$notify) {
                    $resolve = $res;
                    $reject = $rej;
                    $notify = $not;

                    return $promise;
                }
            );

        $mock->expects($expectResolve ? $this->once() : $this->any())
            ->method('resolve')
            ->with(
                $this->anything()
            )->willReturnCallback(
                function ($value = null) use (&$resolve) {
                    if (is_callable($resolve)) {
                        $resolve($value);
                    }
                }
            );

        return $mock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PromiseAdapter
     */
    private function createPromiseMock()
    {
        $mock = $this->getMockBuilder(PromiseAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->any())
            ->method('getAdaptee')
            ->willReturn(
                $this->createReactPromiseMock()
            );

        return $mock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ReactPromise
     */
    private function createReactPromiseMock()
    {
        $mock = $this->getMockBuilder(ReactPromise::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }
}
