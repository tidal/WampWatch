<?php

require_once __DIR__ . '/../bootstrap.php';


use Thruway\Message\SubscribedMessage;

use Tidal\WampWatch\Stub\ClientSessionStub;

class ClientSessionStubTest extends PHPUnit_Framework_TestCase
{

    const PROMISE_CLS = 'React\Promise\Promise';

    /**
     * @var Tidal\WampWatch\Stub\ClientSessionStub
     */
    protected $session;


    public function setUp()
    {
        $this->session = new ClientSessionStub();
    }


    public function test_subscribe_returns_promise()
    {
        $this->assertPromise(
            $this->session->subscribe(
                'foo',
                $this->getEmptyFunc()
            )
        );
    }

    public function test_publish_returns_promise()
    {
        $this->assertPromise(
            $this->session->publish(
                'foo'
            )
        );
    }

    public function test_register_returns_promise()
    {
        $this->assertPromise(
            $this->session->register(
                'foo',
                $this->getEmptyFunc()
            )
        );
    }

    public function test_unregister_returns_promise()
    {
        $this->assertPromise(
            $this->session->unregister(
                'foo'
            )
        );
    }

    public function test_call_returns_promise()
    {
        $this->assertPromise(
            $this->session->call(
                'foo'
            )
        );
    }


    protected function assertPromise($obj)
    {
        $this->assertInstanceOf(self::PROMISE_CLS, $obj);
    }

    protected function getEmptyFunc()
    {
        return function () {
        };
    }


}
