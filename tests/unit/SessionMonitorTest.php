<?php

require_once __DIR__.'/../bootstrap.php';


use Mockery as M;
use Tidal\WampWatch\Adapter\Thruway\ClientSession;
use Tidal\WampWatch\SessionMonitor;
use Tidal\WampWatch\Stub\ClientSessionStub;

/**
 * @author Timo Michna <timomichna@yahoo.de>
 */
class SessionMonitorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
        M::close();
    }

    public function test_starts_returns_true()
    {

        $stub = new ClientSessionStub();
        $monitor = new SessionMonitor($stub);
        $res = $monitor->start();

        $this->assertEquals(true, $res);
    }

    // SUBSCRIPTION AND CALL TESTS

    public function test_start_subscribes_to_join_topic()
    {

        $stub = new ClientSessionStub();
        $monitor = new SessionMonitor($stub);
        $monitor->start();

        $this->assertTrue(
            $stub->hasSubscription(SessionMonitor::SESSION_JOIN_TOPIC)
        );

    }

    public function test_start_subscribes_to_leave_topic()
    {

        $stub = new ClientSessionStub();
        $monitor = new SessionMonitor($stub);
        $monitor->start();

        $this->assertTrue(
            $stub->hasSubscription(SessionMonitor::SESSION_LEAVE_TOPIC)
        );

    }

    public function test_start_calls_session_list()
    {
        $stub = new ClientSessionStub();
        $monitor = new SessionMonitor($stub);
        $monitor->start();

        $this->assertTrue(
            $stub->hasCall(SessionMonitor::SESSION_LIST_TOPIC)
        );

    }


    // SESSION IDS RETRIEVAL TESTS

    public function test_start_retrieves_sessionid_list()
    {
        $stub = new ClientSessionStub();
        $monitor = new SessionMonitor($stub);
        $called = false;

        $stub->setSessionId(321);

        $monitor->on('list', function () use (&$called) {
            $called = true;
        });

        $monitor->start();

        $stub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321]]);


        $this->assertTrue($called);

    }

    public function test_get_sessionids_retrieves_session_list()
    {
        $stub = new ClientSessionStub();
        $stub->setSessionId(321);
        $monitor = new SessionMonitor($stub);
        $listCalled = false;
        $calledBack = false;

        $monitor->on('list', function () use (&$listCalled) {
            $listCalled = true;
        });

        $monitor->getSessionIds(function () use (&$calledBack) {
            $calledBack = true;
        });

        $stub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321]]);


        $this->assertTrue($listCalled && $calledBack);
    }


    public function test_list_event_getsessionid_callback_return_same_value()
    {
        $stub = new ClientSessionStub();
        $stub->setSessionId(321);
        $monitor = new SessionMonitor($stub);
        $listCalled = null;
        $calledBack = null;

        $monitor->on('list', function ($res) use (&$listCalled) {
            $listCalled = $res;
        });

        $monitor->getSessionIds(function ($res) use (&$calledBack) {
            $calledBack = $res;
        });

        $stub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654, 987]]);

        $this->assertSame($listCalled, $calledBack);

    }

    public function test_get_sessionids_removes_monitors_sessionid()
    {

        $stub = new ClientSessionStub();
        $stub->setSessionId(321);
        $monitor = new SessionMonitor($stub);
        $sessionIds = null;

        $monitor->getSessionIds(function (array $ids) use (&$sessionIds) {
            $sessionIds = $ids;
        });

        $stub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654, 987]]);

        $this->assertNotContains(321, $sessionIds);
    }

    public function test_second_get_sessionids_retrieves_ids_locally()
    {

        $stub = new ClientSessionStub();
        $stub->setSessionId(321);
        $monitor = new SessionMonitor($stub);
        $firstResult = null;
        $secondResult = null;

        $monitor->getSessionIds(function (array $ids) use (&$firstResult) {
            $firstResult = $ids;
        });

        $stub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654, 987]]);

        $monitor->getSessionIds(function (array $ids) use (&$secondResult) {
            $secondResult = $ids;
        });

        $this->assertSame($firstResult, $secondResult);

    }
    
    


}
