<?php

namespace Tidal\WampWatch\Test\Unit\Adapter;

require_once __DIR__ . '/../../bootstrap.php';

use Tidal\WampWatch\Adapter\Thruway\ClientSession;
use Mockery as M;
use Mockery\MockInterface;
use Tidal\WampWatch\Adapter\React\PromiseFactory;
use Tidal\WampWatch\Adapter\React\PromiseAdapter;
use Tidal\WampWatch\Test\Unit\Stub\PromiseStub;

class ThruwaySessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MockInterface
     */
    protected $sessionMock;

    /**
     * @var ClientSession
     */
    protected $adapter;

    /**
     * @var PromiseFactory
     */
    protected $promiseFactoryMock;

    public function setUp()
    {
        $this->sessionMock = M::mock('\Thruway\ClientSession');
        $this->promiseFactoryMock = $this->getPromiseFactoryMock();
        $this->adapter = new ClientSession(
            $this->sessionMock,
            $this->promiseFactoryMock
        );
    }

    public function test_subscribe()
    {
        $promise = M::mock('React\Promise\Promise');
        $cb = $this->getEmptyFunc();
        $options = new \stdClass();
        $options->foo = 'bar';

        $this->sessionMock->shouldReceive('subscribe')
            ->once()
            ->with('foo', $cb, $options)
            ->andReturn($promise);

        $this->adapter->subscribe('foo', $cb, $options);
    }

    public function test_publish()
    {
        $promise = M::mock('React\Promise\Promise');
        $options = new \stdClass();
        $options->foo = 'bar';
        $args = ['foo'];
        $argsKW = ['foo' => 'bar'];

        $this->sessionMock->shouldReceive('publish')
            ->once()
            ->with('foo', $args, $argsKW, $options)
            ->andReturn($promise);

        $this->adapter->publish('foo', $args, $argsKW, $options);
    }

    public function test_register()
    {
        $promise = M::mock('React\Promise\Promise');
        $cb = $this->getEmptyFunc();
        $options = new \stdClass();
        $options->foo = 'bar';

        $this->sessionMock->shouldReceive('register')
            ->once()
            ->with('foo', $cb, $options)
            ->andReturn($promise);

        $this->adapter->register('foo', $cb, $options);
    }

    public function test_unregister()
    {
        $promise = M::mock('React\Promise\Promise');

        $this->sessionMock->shouldReceive('unregister')
            ->once()
            ->with('foo')
            ->andReturn($promise);

        $this->adapter->unregister('foo');
    }

    public function test_call()
    {
        $promise = M::mock('React\Promise\Promise');
        $options = new \stdClass();
        $options->foo = 'bar';
        $args = ['foo'];
        $argsKW = ['foo' => 'bar'];

        $this->sessionMock->shouldReceive('call')
            ->once()
            ->with('foo', $args, $argsKW, $options)
            ->andReturn($promise);

        $this->adapter->call('foo', $args, $argsKW, $options);
    }

    public function test_send_message()
    {
        $msg = M::mock('Thruway\Message\Message');

        $this->sessionMock->shouldReceive('sendMessage')
            ->once()
            ->with($msg);

        $this->adapter->sendMessage($msg);
    }

    public function test_set_session_id()
    {
        $this->sessionMock->shouldReceive('setSessionId')
            ->once()
            ->with(321);

        $this->adapter->setSessionId(321);
    }

    public function test_get_session_id()
    {
        $this->sessionMock->shouldReceive('getSessionId')
            ->once()
            ->andReturn(321);

        $this->assertSame(321, $this->adapter->getSessionId());
    }

    private function getEmptyFunc()
    {
        return function () {
        };
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
