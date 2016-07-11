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

    /**
     *
     */
    public function setUp()
    {
        $this->session = new ClientSessionStub();
    }


    /**
     *
     */
    public function test_subscribe_returns_promise()
    {
        $this->assertPromise(
            $this->session->subscribe(
                'foo',
                $this->getEmptyFunc()
            )
        );
    }


    /**
     *
     */
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

    /**
     *
     */
    function test_complete_subscrition_throws_exception_on_unknown_subscription()
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

    /**
     *
     */
    public function test_publish_returns_promise()
    {
        $this->assertPromise(
            $this->session->publish(
                'foo'
            )
        );
    }

    /**
     *
     */
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


    /**
     *
     */
    function test_confirm_publication_throws_exception_on_unknown_subscription()
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


    /**
     *
     */
    public function test_register_returns_promise()
    {
        $this->assertPromise(
            $this->session->register(
                'foo',
                $this->getEmptyFunc()
            )
        );
    }


    /**
     *
     */
    public function test_registration_can_be_confirmed()
    {

        $registered = null;
        $promise = $this->session->publish(
            'foo'
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


    /**
     *
     */
    public function test_unregister_returns_promise()
    {
        $this->assertPromise(
            $this->session->unregister(
                'foo'
            )
        );
    }

    /**
     *
     */
    public function test_call_returns_promise()
    {
        $this->assertPromise(
            $this->session->call(
                'foo'
            )
        );
    }

    /**
     * asserts an object to be a promise
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
