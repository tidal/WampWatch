<?php

require_once __DIR__.'/../bootstrap.php';


use Mockery as M;
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

    // START MONITOR TESTS

    public function test_starts_returns_true()
    {

        $stub = new ClientSessionStub();
        $monitor = new SessionMonitor($stub);
        $res = $monitor->start();

        $this->assertEquals(true, $res);
    }

    public function test_is_not_running_before_started()
    {

        $stub = new ClientSessionStub();
        $monitor = new SessionMonitor($stub);

        $this->assertFalse($monitor->isRunning());
    }

    public function test_is_not_running_before_onjoin_subscriptions()
    {

        $stub = new ClientSessionStub();
        $monitor = new SessionMonitor($stub);
        $monitor->start();
        $stub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321]]);
        $stub->completeSubscription(SessionMonitor::SESSION_LEAVE_TOPIC);

        $this->assertFalse($monitor->isRunning());
    }

    public function test_is_not_running_before_onleave_subscriptions()
    {

        $stub = new ClientSessionStub();
        $monitor = new SessionMonitor($stub);
        $monitor->start();
        $stub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321]]);
        $stub->completeSubscription(SessionMonitor::SESSION_JOIN_TOPIC);

        $this->assertFalse($monitor->isRunning());
    }

    public function test_is_not_running_before_list_response()
    {

        $stub = new ClientSessionStub();
        $monitor = new SessionMonitor($stub);
        $monitor->start();
        $stub->completeSubscription(SessionMonitor::SESSION_JOIN_TOPIC);
        $stub->completeSubscription(SessionMonitor::SESSION_LEAVE_TOPIC);

        $this->assertFalse($monitor->isRunning());
    }

    public function test_is_running_after_subscriptions_and_list()
    {

        $stub = new ClientSessionStub();
        $monitor = new SessionMonitor($stub);
        $monitor->start();
        $stub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321]]);
        $stub->completeSubscription(SessionMonitor::SESSION_JOIN_TOPIC);
        $stub->completeSubscription(SessionMonitor::SESSION_LEAVE_TOPIC);

        $this->assertTrue($monitor->isRunning());
    }

    public function test_start_event_after_running()
    {

        $stub = new ClientSessionStub();
        $monitor = new SessionMonitor($stub);
        $stub->setSessionId(321);
        $response = null;

        $monitor->on('list', function ($res) use (&$response) {
            $response = $res;
        });

        $monitor->start();
        $stub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654]]);
        $stub->completeSubscription(SessionMonitor::SESSION_JOIN_TOPIC);
        $stub->completeSubscription(SessionMonitor::SESSION_LEAVE_TOPIC);

        $this->assertEquals([654], $response);
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

        $monitor->getSessionIds()->done(function () use (&$calledBack) {
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

        $monitor->getSessionIds()->done(function ($res) use (&$calledBack) {
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

        $monitor->getSessionIds()->done(function (array $ids) use (&$sessionIds) {
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

        $monitor->getSessionIds()->done(function (array $ids) use (&$firstResult) {
            $firstResult = $ids;
        });

        $stub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654, 987]]);

        $monitor->getSessionIds()->done(function (array $ids) use (&$secondResult) {
            $secondResult = $ids;
        });

        $this->assertSame($firstResult, $secondResult);

    }

    public function test_session_registration_on_start()
    {

        $stub = new ClientSessionStub();
        $stub->setSessionId(321);
        $monitor = new SessionMonitor($stub);

        $monitor->start();
        $stub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654]]);

        $this->assertTrue($monitor->hasSessionId(654));
    }

    // SESSION JOIN TESTS


    public function test_session_registration_on_join()
    {

        $stub = new ClientSessionStub();
        $stub->setSessionId(321);
        $monitor = new SessionMonitor($stub);

        $monitor->start();

        $sessionInfo = new stdClass();
        $sessionInfo->session = 654;

        $stub->emit(SessionMonitor::SESSION_JOIN_TOPIC, [[$sessionInfo]]);

        $this->assertTrue($monitor->hasSessionId(654));
    }

    public function test_session_unregistration_on_leave()
    {
        $stub = new ClientSessionStub();
        $stub->setSessionId(321);
        $monitor = new SessionMonitor($stub);

        $monitor->start();
        $stub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654]]);

        $stub->emit(SessionMonitor::SESSION_LEAVE_TOPIC, [[654]]);

        $this->assertFalse($monitor->hasSessionId(654));
    }

    public function test_unknown_session_unregistration_causes_no_error()
    {

        $stub = new ClientSessionStub();
        $stub->setSessionId(321);
        $monitor = new SessionMonitor($stub);

        $monitor->start();

        $stub->emit(SessionMonitor::SESSION_LEAVE_TOPIC, [[654]]);

        $this->assertFalse($monitor->hasSessionId(654));
    }

    public function test_session_join_emits_event()
    {
        $stub = new ClientSessionStub();
        $stub->setSessionId(321);
        $monitor = new SessionMonitor($stub);
        $monitor->start();
        $response = null;

        $monitor->on('join', function ($res) use (&$response) {
            $response = $res;
        });

        $sessionInfo = new stdClass();
        $sessionInfo->session = 654;

        $stub->emit(SessionMonitor::SESSION_JOIN_TOPIC, [[$sessionInfo]]);

        $this->assertSame($sessionInfo, $response);
    }

    public function test_session_leave_emits_event()
    {
        $stub = new ClientSessionStub();
        $stub->setSessionId(321);
        $monitor = new SessionMonitor($stub);
        $monitor->start();
        $response = null;

        $stub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654]]);

        $monitor->on('leave', function ($res) use (&$response) {
            $response = $res;
        });

        $stub->emit(SessionMonitor::SESSION_LEAVE_TOPIC, [[654]]);

        $this->assertSame(654, $response);
    }

    // SESSION INFO TESTS


    public function test_get_sessioninfo_calls_promise()
    {
        $stub = new ClientSessionStub();
        $stub->setSessionId(321);
        $monitor = new SessionMonitor($stub);
        $response = null;

        $monitor->getSessionInfo(654)->done(function ($res) use (&$response) {
            $response = $res;
        });

        $sessionInfo = new stdClass();
        $sessionInfo->session = 654;

        $stub->respondToCall(SessionMonitor::SESSION_INFO_TOPIC, $sessionInfo);

        $this->assertSame($sessionInfo, $response);
    }

    public function test_get_sessioninfo_calls_event()
    {
        $stub = new ClientSessionStub();
        $stub->setSessionId(321);
        $monitor = new SessionMonitor($stub);
        $response = null;

        $monitor->on('info', function ($res) use (&$response) {
            $response = $res;
        });

        $monitor->getSessionInfo(654);

        $sessionInfo = new stdClass();
        $sessionInfo->session = 654;

        $stub->respondToCall(SessionMonitor::SESSION_INFO_TOPIC, $sessionInfo);

        $this->assertSame($sessionInfo, $response);
    }

    public function test_get_sessioninfo_fail_emits_event()
    {
        $stub = new ClientSessionStub();
        $stub->setSessionId(321);
        $monitor = new SessionMonitor($stub);
        $response = null;

        $monitor->on('error', function ($res) use (&$response) {
            $response = $res;
        });

        $monitor->getSessionInfo(654);

        $sessionInfo = new stdClass();
        $sessionInfo->session = 654;

        $stub->failCall(SessionMonitor::SESSION_INFO_TOPIC, $sessionInfo);

        $this->assertSame($sessionInfo, $response);
    }

    public function test_invalid_sessioninfo_does_not_get_added()
    {
        $stub = new ClientSessionStub();
        $stub->setSessionId(321);
        $monitor = new SessionMonitor($stub);

        $sessionInfo = new stdClass();
        $sessionInfo->id = 654;

        $monitor->start();

        $stub->completeSubscription(SessionMonitor::SESSION_JOIN_TOPIC);

        $stub->emit(SessionMonitor::SESSION_JOIN_TOPIC, [[$sessionInfo]]);

        $this->assertFalse($monitor->hasSessionId(654));
    }

    // STOP MONITOR TESTS

    public function test_is_not_running_after_stop()
    {

        $stub = new ClientSessionStub();
        $monitor = new SessionMonitor($stub);

        $monitor->start();
        $stub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654]]);
        $stub->completeSubscription(SessionMonitor::SESSION_JOIN_TOPIC);
        $stub->completeSubscription(SessionMonitor::SESSION_LEAVE_TOPIC);

        $monitor->stop();

        $this->assertFalse($monitor->isRunning());
    }

    public function test_stop_emits_event()
    {

        $stub = new ClientSessionStub();
        $monitor = new SessionMonitor($stub);
        $monitor->start();
        $response = null;

        $monitor->on('stop', function ($res) use (&$response) {
            $response = $res;
        });

        $stub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654]]);
        $stub->completeSubscription(SessionMonitor::SESSION_JOIN_TOPIC);
        $stub->completeSubscription(SessionMonitor::SESSION_LEAVE_TOPIC);

        $monitor->stop();

        $this->assertSame($monitor, $response);
    }

}
