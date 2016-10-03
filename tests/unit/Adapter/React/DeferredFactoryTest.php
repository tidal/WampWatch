<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\unit\Adapter\React;

use Tidal\WampWatch\Adapter\React\DeferredFactory;
use Tidal\WampWatch\Adapter\React\PromiseFactory;
use Tidal\WampWatch\Adapter\React\PromiseAdapter;
use Tidal\WampWatch\Adapter\React\DeferredAdapter;
use React\Promise\Deferred as ReactDeferred;
use Tidal\WampWatch\Test\Unit\Stub\PromiseStub;

class DeferredFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DeferredFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new DeferredFactory(
            $this->getPromiseFactoryMock()
        );
    }

    public function test_can_create()
    {
        $this->assertInstanceOf(DeferredAdapter::class, $this->factory->create());
    }

    public function test_create_wraps_react_deferred()
    {
        $this->assertInstanceOf(ReactDeferred::class, $this->factory->create()->getAdaptee());
    }

    public function test_can_access_promise_factory()
    {
        $this->assertInstanceOf(PromiseFactory::class, $this->factory->getPromiseFactory());
    }

    public function test_create_sets_promise_factory_on_deferred()
    {
        $promiseFactory = $this->getPromiseFactoryMock();
        $factory = new DeferredFactory(
            $promiseFactory
        );
        $promise = $factory->create();

        $this->assertSame($promiseFactory, $promise->getPromiseFactory());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PromiseFactory
     */
    private function getPromiseFactoryMock()
    {
        $mock = $this->getMockBuilder(PromiseFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->any())
            ->method('create')
            ->willReturn(
                $this->getPromiseMock()
            );

        return $mock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PromiseStub
     */
    private function getPromiseMock()
    {
        return $this->getMockBuilder(PromiseAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
