<?php
/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Test\Unit;

require_once __DIR__ . '/../bootstrap.php';

use Tidal\WampWatch\Stub\ClientSessionStub;
use Tidal\WampWatch\Exception\UnknownProcedureException;
use Tidal\WampWatch\Exception\UnknownTopicException;
use Thruway\Message\ErrorMessage as ThruwayErrorMessage;
use stdClass;

class ClientSessionStubTest extends \PHPUnit_Framework_TestCase
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

            $this->fail('An UnknownTopicException should have been thrown');
        } catch (UnknownTopicException $e) {
            $this->assertSame('foo', $e->getTopicName());
        }

    }

    /**
     *
     */

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

    /**
     *
     */
    public function test_has_subscription()
    {
        $this->assertFalse($this->session->hasSubscription('foo'));

        $this->session->subscribe(
            'foo',
            $this->getEmptyFunc()
        );

        $this->assertTrue($this->session->hasSubscription('foo'));
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
    function test_confirm_publication_throws_exception_on_unknown_publication()
    {
        try {
            $this->session->confirmPublication(
                'foo',
                321,
                654
            );

            $this->fail('An UnknownTopicException should have been thrown');
        } catch (UnknownTopicException $e) {
            $this->assertSame('foo', $e->getTopicName());
        }

    }

    /**
     *
     */
    public function test_publication_can_be_failed()
    {

        $published = null;
        $promise = $this->session->publish(
            'foo'
        );

        $promise->otherwise(function ($message) use (&$published) {
            $published = $message;
        });

        $this->session->failPublication(
            'foo',
            321
        );
        $this->assertInstanceOf(
            'Thruway\Message\ErrorMessage',
            $published
        );

    }

    /**
     *
     */
    function test_fail_publication_throws_exception_on_unknown_publication()
    {
        try {
            $this->session->failPublication(
                'foo',
                321
            );

            $this->fail('An UnknownTopicException should have been thrown');
        } catch (UnknownTopicException $e) {
            $this->assertSame('foo', $e->getTopicName());
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


    /**
     *
     */
    public function test_confirm_registration_throws_exception_on_unknown_registration()
    {
        try {
            $this->session->confirmRegistration(
                'foo',
                321,
                654
            );

            $this->fail('An UnknownProcedureException should have been thrown');
        } catch (UnknownProcedureException $e) {
            $this->assertSame('foo', $e->getProcedureName());
        }

    }

    /**
     *
     */
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

    /**
     *
     */
    public function test_call_registration_throws_exception_on_unknown_procedure()
    {
        try {
            $this->session->callRegistration('foo', [1, 2]);

            $this->fail('An UnknownProcedureException should have been thrown');
        } catch (UnknownProcedureException $e) {
            $this->assertSame('foo', $e->getProcedureName());
        }

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


    /**
     *
     */
    function test_confirm_unregistration_throws_exception_on_unknown_unregistration()
    {
        try {
            $this->session->confirmUnregistration(
                'foo',
                321
            );

            $this->fail('An UnknownProcedureException should have been thrown');
        } catch (UnknownProcedureException $e) {
            $this->assertSame('foo', $e->getProcedureName());
        }

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
     *
     */
    public function test_call_can_be_responded_to()
    {
        $response = null;

        $this->session->call('foo')->then(function ($res) use (&$response) {
            $response = $res;
        });

        $this->session->respondToCall('foo', 'bar');

        $this->assertSame('bar', $response);
    }

    public function test_has_call()
    {
        $this->assertFalse($this->session->hasCall('foo'));

        $this->session->call('foo');

        $this->assertTrue($this->session->hasCall('foo'));
    }


    /**
     *
     */
    function test_respond_to_callthrows_exception_on_unknown_call()
    {
        try {
            $this->session->respondToCall(
                'foo',
                321
            );

            $this->fail('An UnknownProcedureException should have been thrown');
        } catch (UnknownProcedureException $e) {
            $this->assertSame('foo', $e->getProcedureName());
        }

    }

    /**
     *
     */
    function test_call_can_be_failed()
    {
        $error = $this->getErrorMessage();

        $response = null;

        $this->session->call('foo')->otherwise(function ($err) use (&$response) {
            $response = $err;
        });

        $this->session->failCall('foo', $error);

        $this->assertSame($error, $response);

    }


    /**
     *
     */
    function test_fail_call_throws_exception_on_unknown_call()
    {

        $this->setExpectedException(UnknownProcedureException::class);

        $this->session->failCall('foo', $this->getErrorMessage());

    }

    // ACCESSOR TESTS

    /**
     *
     */
    public function test_sessionid_accessors()
    {
        $sessionId = 321;

        $this->session->setSessionId($sessionId);

        $this->assertEquals($sessionId, $this->session->getSessionId());
    }

    /**
     *
     */
    public function test_get_uniqueid()
    {
        $id = ClientSessionStub::getUniqueId();

        // test 10 unique ids
        for ($x = 0; $x < 10; $x++) {
            $this->assertNotEquals($id, ClientSessionStub::getUniqueId());
        }

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
    private function getEmptyFunc()
    {
        return function () {
        };
    }

    private function getErrorMessage()
    {
        return new ThruwayErrorMessage(
            "wamp.error.not_authorized",
            321,
            new stdClass(),
            "foo",
            ["session is not authorized to do 'foo'"],
            new stdClass()
        );
    }

}
