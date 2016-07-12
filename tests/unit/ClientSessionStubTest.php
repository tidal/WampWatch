<?php

require_once __DIR__.'/../bootstrap.php';


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


    public function test_subscribe_can_be_completed()
    {
        $subscribed = null;
        $promise = $this->session->subscribe(
            'foo',
            $this->getEmptyFunc()
        );

        $promise->then(function ($message) use (&$subscribed) {
            $subscribed = $message;
        });

        $this->session->completeSubscription(
            'foo',
            321,
            654
        );
        $this->assertInstanceOf(
            'Thruway\Message\SubscribedMessage',
            $subscribed
        );
    }


    public function test_complete_subscrition_throws_exception_on_unknown_subscription()
    {
        try {
            $this->session->completeSubscription(
                'foo',
                321,
                654
            );

            $this->fail('A RuntimeException should have been thrown');
        } catch (\RuntimeException $e) {
        }
    }


    public function test_subscription_can_be_published_to()
    {
        $result = null;

        $this->session->subscribe(
            'foo',
            function ($res) use (&$result) {
                $result = $res;
            }
        );

        $this->session->emit('foo', ['bar']);

        $this->assertEquals('bar', $result);
    }


    public function test_publish_returns_promise()
    {
        $this->assertPromise(
            $this->session->publish(
                'foo'
            )
        );
    }


    public function test_publication_can_be_confirmed()
    {
        $published = null;
        $promise = $this->session->publish(
            'foo'
        );

        $promise->then(function ($message) use (&$published) {
            $published = $message;
        });

        $this->session->confirmPublication(
            'foo',
            321,
            654
        );
        $this->assertInstanceOf(
            'Thruway\Message\PublishedMessage',
            $published
        );
    }


    public function test_confirm_publication_throws_exception_on_unknown_publication()
    {
        try {
            $this->session->confirmPublication(
                'foo',
                321,
                654
            );

            $this->fail('A RuntimeException should have been thrown');
        } catch (\RuntimeException $e) {
        }
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


    public function test_registration_can_be_confirmed()
    {
        $registered = null;
        $promise = $this->session->register(
            'foo',
            $this->getEmptyFunc()
        );

        $promise->then(function ($message) use (&$registered) {
            $registered = $message;
        });

        $this->session->confirmRegistration(
            'foo',
            321,
            654
        );
        $this->assertInstanceOf(
            'Thruway\Message\RegisteredMessage',
            $registered
        );
    }


    public function test_confirm_registration_throws_exception_on_unknown_registration()
    {
        try {
            $this->session->confirmRegistration(
                'foo',
                321,
                654
            );

            $this->fail('A RuntimeException should have been thrown');
        } catch (\RuntimeException $e) {
        }
    }


    public function test_registration_can_be_called()
    {
        $registered = null;
        $this->session->register(
            'foo',
            function ($args) {
                return $args[0] + $args[1];
            }
        );

        $res = $this->session->callRegistration('foo', [1, 2]);

        $this->assertEquals(3, $res);
    }


    public function test_unregister_returns_promise()
    {
        $this->assertPromise(
            $this->session->unregister(
                'foo'
            )
        );
    }


    public function test_unregistration_can_be_confirmed()
    {
        $unregistered = null;
        $promise = $this->session->unregister(
            'foo'
        );

        $promise->then(function ($message) use (&$unregistered) {
            $unregistered = $message;
        });

        $this->session->confirmUnregistration(
            'foo',
            321
        );
        $this->assertInstanceOf(
            'Thruway\Message\UnregisteredMessage',
            $unregistered
        );
    }


    public function test_confirm_unregistration_throws_exception_on_unknown_unregistration()
    {
        try {
            $this->session->confirmUnregistration(
                'foo',
                321
            );

            $this->fail('A RuntimeException should have been thrown');
        } catch (\RuntimeException $e) {
        }
    }


    public function test_call_returns_promise()
    {
        $this->assertPromise(
            $this->session->call(
                'foo'
            )
        );
    }


    public function test_call_can_be_responded_to()
    {
        $response = null;

        $this->session->call('foo')->then(function ($res) use (&$response) {
            $response = $res;
        });

        $this->session->respondToCall('foo', 'bar');

        $this->assertSame('bar', $response);
    }


    public function test_respond_to_callthrows_exception_on_unknown_call()
    {
        try {
            $this->session->respondToCall(
                'foo',
                321
            );

            $this->fail('A RuntimeException should have been thrown');
        } catch (\RuntimeException $e) {
        }
    }


    public function test_sessionid_accessors()
    {
        $sessionId = 321;

        $this->session->setSessionId($sessionId);

        $this->assertEquals($sessionId, $this->session->getSessionId());
    }


    public function test_get_uniqueid()
    {
        $id = ClientSessionStub::getUniqueId();

        // test 10 unique ids
        for ($x = 0; $x < 10; $x++) {
            $this->assertNotEquals($id, ClientSessionStub::getUniqueId());
        }
    }

    /**
     * asserts an object to be a promise.
     *
     * @param $obj
     */
    protected function assertPromise($obj)
    {
        $this->assertInstanceOf(self::PROMISE_CLS, $obj);
    }

    /**
     * convenient function to create an empty function.
     *
     * @return \Closure
     */
    protected function getEmptyFunc()
    {
        return function () {
        };
    }
}