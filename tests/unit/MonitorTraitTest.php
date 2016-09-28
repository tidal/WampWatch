<?php

require_once __DIR__.'/../bootstrap.php';
require_once __DIR__.'/stub/MonitorTraitImplementation.php';

use Tidal\WampWatch\Stub\ClientSessionStub;
use Tidal\WampWatch\Subscription\Collection;
use Mockery as M;

/**
 * @author Timo Michna <timomichna@yahoo.de>
 */
class MonitorTraitTest extends PHPUnit_Framework_TestCase
{
    protected $mock,
        $session,
        $monitorStub,
        $lastEvent,
        $lastArgs;

    public function setUp()
    {
        $this->lastEvent = '';
        $this->lastArgs = [];
    }

    public function test_get_server_session()
    {

        $stub = new ClientSessionStub();
        $monitor = new MonitorTraitImplementation($stub);
        $this->assertEquals($stub, $monitor->getServerSession());
    }

    public function test_start()
    {
        $lastEvent = '';
        $lastArgs = [];
        $stub = new ClientSessionStub();
        $monitor = new MonitorTraitImplementation($stub);
        $monitor->on('start', function () use (&$lastEvent, &$lastArgs) {
            $lastEvent = 'start';
            $lastArgs = func_get_args();
        });

        $res = $monitor->start();

        $this->assertSame(true, $res);
        $this->assertSame('start', $lastEvent);
        $this->assertTrue(is_array($lastArgs));
        $this->assertSame(true, $monitor->isRunning(), 'monitor should be running, when it was started');
    }

    public function test_stop_without_being_started()
    {
        $lastEvent = '';
        $lastArgs = [];
        $stub = new ClientSessionStub();
        $monitor = new MonitorTraitImplementation($stub);
        $monitor->on('stop', function () use (&$lastEvent, &$lastArgs) {
            $lastEvent = 'start';
            $lastArgs = func_get_args();
        });
        $res = $monitor->stop();

        $this->assertSame(false, $res, "'stop' should return false, when monitor was not started");
        $this->assertSame('', $lastEvent, "'stop' event should not trigger, when monitor was not started");
    }

    public function test_stop_with_being_started()
    {
        $lastEvent = '';
        $lastArgs = [];
        $stub = new ClientSessionStub();
        $monitor = new MonitorTraitImplementation($stub);
        $monitor->on('stop', function () use (&$lastEvent, &$lastArgs) {
            $lastEvent = 'stop';
            $lastArgs = func_get_args();
        });

        $monitor->start();
        $res = $monitor->stop();

        $this->assertSame(true, $res, "'stop' should return true, when monitor was stopped");
        $this->assertSame('stop', $lastEvent, "'stop' event should not trigger, when monitor was not started");
        $this->assertSame($monitor, $lastArgs[0]);
        $this->assertSame(false, $monitor->isRunning(), 'monitor should not be running, when it was stopped');
    }


    public function test_starts_returns_true()
    {

        $stub = new ClientSessionStub();
        $monitor = new MonitorTraitImplementation($stub);
        $res = $monitor->start();

        $this->assertEquals(true, $res);
    }

    public function test_is_not_running_before_started()
    {

        $stub = new ClientSessionStub();
        $monitor = new MonitorTraitImplementation($stub);

        $this->assertFalse($monitor->isRunning());
    }

    public function test_is_not_running_before_meta_subscriptions()
    {
        $stub = new ClientSessionStub();
        $monitor = new MonitorTraitImplementation($stub);
        $collection = new Collection($stub);
        $collection->addSubscription('foo', $this->getEmptyFunc());
        $monitor->setMetaSubscriptionCollection($collection);
        $monitor->setInitialCall('bar', $this->getEmptyFunc());
        $monitor->start();

        $stub->respondToCall('bar', [[321]]);

        $this->assertFalse($monitor->isRunning());
    }


    public function test_is_not_running_before_initial_call()
    {
        $stub = new ClientSessionStub();
        $monitor = new MonitorTraitImplementation($stub);
        $collection = new Collection($stub);
        $collection->addSubscription('foo', $this->getEmptyFunc());
        $monitor->setMetaSubscriptionCollection($collection);
        $monitor->setInitialCall('bar', $this->getEmptyFunc());
        $monitor->start();

        $stub->completeSubscription('foo');

        $this->assertFalse($monitor->isRunning());
    }

    public function test_is_running_after_subscriptions_and_list()
    {
        $stub = new ClientSessionStub();
        $monitor = new MonitorTraitImplementation($stub);
        $collection = new Collection($stub);
        $collection->addSubscription('foo', $this->getEmptyFunc());
        $monitor->setMetaSubscriptionCollection($collection);
        $monitor->setInitialCall('bar', $this->getEmptyFunc());
        $monitor->start();

        $stub->completeSubscription('foo');
        $stub->respondToCall('bar', [[321]]);

        $this->assertTrue($monitor->isRunning());
    }

    public function test_start_event_after_running()
    {
        $stub = new ClientSessionStub();
        $monitor = new MonitorTraitImplementation($stub);
        $collection = new Collection($stub);
        $collection->addSubscription('foo', $this->getEmptyFunc());
        $monitor->setMetaSubscriptionCollection($collection);
        $monitor->setInitialCall('bar', $this->getEmptyFunc());

        $called = null;

        $monitor->on('start', function () use (&$called) {
            $called = true;
        });

        $monitor->start();

        $stub->completeSubscription('foo');
        $stub->respondToCall('bar', [[321]]);

        $this->assertTrue($called);
    }

    // SUBSCRIPTION AND CALL TESTS

    public function test_start_subscribes_to_topic()
    {
        $stub = new ClientSessionStub();
        $monitor = new MonitorTraitImplementation($stub);
        $collection = new Collection($stub);
        $collection->addSubscription('foo', $this->getEmptyFunc());
        $monitor->setMetaSubscriptionCollection($collection);
        $monitor->setInitialCall('bar', $this->getEmptyFunc());
        $monitor->start();

        $this->assertTrue(
            $stub->hasSubscription('foo')
        );

    }

    public function test_start_calls_procedure()
    {
        $stub = new ClientSessionStub();
        $monitor = new MonitorTraitImplementation($stub);
        $collection = new Collection($stub);
        $collection->addSubscription('foo', $this->getEmptyFunc());
        $monitor->setMetaSubscriptionCollection($collection);
        $monitor->setInitialCall('bar', $this->getEmptyFunc());
        $monitor->start();

        $this->assertTrue(
            $stub->hasCall('bar')
        );

    }

    public function test_failed_initial_procedure_emits_event()
    {
        $stub = new ClientSessionStub();
        $monitor = new MonitorTraitImplementation($stub);
        $called = null;

        $monitor->on('error', function () use (&$called) {
            $called = true;
        });

        $monitor->setInitialCall('bar', $this->getEmptyFunc());
        $monitor->start();

        $stub->failCall('bar', 'foo');

        $this->assertTrue($called);
    }


    // STOP MONITOR TESTS


    public function test_is_not_running_after_stop()
    {
        $stub = new ClientSessionStub();
        $monitor = new MonitorTraitImplementation($stub);
        $collection = new Collection($stub);
        $collection->addSubscription('foo', $this->getEmptyFunc());
        $monitor->setMetaSubscriptionCollection($collection);
        $monitor->setInitialCall('bar', $this->getEmptyFunc());

        $monitor->start();

        $stub->completeSubscription('foo');
        $stub->respondToCall('bar', [[321]]);

        $monitor->stop();

        $this->assertFalse($monitor->isRunning());
    }

    public function test_stop_emits_event()
    {
        $stub = new ClientSessionStub();
        $monitor = new MonitorTraitImplementation($stub);
        $collection = new Collection($stub);
        $collection->addSubscription('foo', $this->getEmptyFunc());
        $monitor->setMetaSubscriptionCollection($collection);
        $monitor->setInitialCall('bar', $this->getEmptyFunc());

        $monitor->start();

        $response = null;

        $monitor->on('stop', function ($res) use (&$response) {
            $response = $res;
        });

        $stub->completeSubscription('foo');
        $stub->respondToCall('bar', [[321]]);

        $monitor->stop();

        $this->assertSame($monitor, $response);
    }

    private function getEmptyFunc()
    {
        return function () {
        };
    }
}
