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

require_once __DIR__.'/../bootstrap.php';

use Mockery as M;
use Tidal\WampWatch\SessionMonitor;
use Tidal\WampWatch\Stub\ClientSessionStub;
use PHPUnit_Framework_TestCase;
use stdClass;
use Thruway\Message;

/**
 * @author Timo Michna <timomichna@yahoo.de>
 */
class SessionMonitorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ClientSessionStub
     */
    private $sessionStub;

    /**
     * @var SessionMonitor
     */
    private $monitor;

    public function setUp()
    {
        $this->sessionStub = new ClientSessionStub();
        $this->monitor = new SessionMonitor($this->sessionStub);
    }

    public function tearDown()
    {
        M::close();
    }

    // START MONITOR TESTS

    public function test_starts_returns_true()
    {
        $res = $this->monitor->start();

        $this->assertEquals(true, $res);
    }

    public function test_is_not_running_before_started()
    {
        $this->assertFalse($this->monitor->isRunning());
    }

    public function test_is_not_running_before_onjoin_subscriptions()
    {
        $this->monitor->start();
        $this->sessionStub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321]]);
        $this->sessionStub->completeSubscription(SessionMonitor::SESSION_LEAVE_TOPIC);

        $this->assertFalse($this->monitor->isRunning());
    }

    public function test_is_not_running_before_onleave_subscriptions()
    {
        $this->monitor->start();
        $this->sessionStub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321]]);
        $this->sessionStub->completeSubscription(SessionMonitor::SESSION_JOIN_TOPIC);

        $this->assertFalse($this->monitor->isRunning());
    }

    public function test_is_not_running_before_list_response()
    {
        $this->monitor->start();
        $this->sessionStub->completeSubscription(SessionMonitor::SESSION_JOIN_TOPIC);
        $this->sessionStub->completeSubscription(SessionMonitor::SESSION_LEAVE_TOPIC);

        $this->assertFalse($this->monitor->isRunning());
    }

    public function test_is_running_after_subscriptions_and_list()
    {
        $this->monitor->start();
        $this->sessionStub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321]]);
        $this->sessionStub->completeSubscription(SessionMonitor::SESSION_JOIN_TOPIC);
        $this->sessionStub->completeSubscription(SessionMonitor::SESSION_LEAVE_TOPIC);

        $this->assertTrue($this->monitor->isRunning());
    }

    public function test_start_event_after_running()
    {
        $this->sessionStub->setSessionId(321);
        $response = null;

        $this->monitor->on('list', function ($res) use (&$response) {
            $response = $res;
        });

        $this->monitor->start();
        $this->sessionStub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654]]);
        $this->sessionStub->completeSubscription(SessionMonitor::SESSION_JOIN_TOPIC);
        $this->sessionStub->completeSubscription(SessionMonitor::SESSION_LEAVE_TOPIC);

        $this->assertEquals([654], $response);
    }

    // SUBSCRIPTION AND CALL TESTS

    public function test_start_subscribes_to_join_topic()
    {
        $this->monitor->start();

        $this->assertTrue(
            $this->sessionStub->hasSubscription(SessionMonitor::SESSION_JOIN_TOPIC)
        );
    }

    public function test_start_subscribes_to_leave_topic()
    {
        $this->monitor->start();

        $this->assertTrue(
            $this->sessionStub->hasSubscription(SessionMonitor::SESSION_LEAVE_TOPIC)
        );
    }

    public function test_start_calls_session_list()
    {
        $this->monitor->start();

        $this->assertTrue(
            $this->sessionStub->hasCall(SessionMonitor::SESSION_LIST_TOPIC)
        );
    }

    // SESSION IDS RETRIEVAL TESTS

    public function test_start_retrieves_sessionid_list()
    {
        $called = false;

        $this->sessionStub->setSessionId(321);

        $this->monitor->on('list', function () use (&$called) {
            $called = true;
        });

        $this->monitor->start();

        $this->sessionStub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321]]);

        $this->assertTrue($called);
    }

    public function test_get_sessionids_retrieves_session_list()
    {

        $this->sessionStub->setSessionId(321);
        $listCalled = false;
        $calledBack = false;

        $this->monitor->on('list', function () use (&$listCalled) {
            $listCalled = true;
        });

        $this->monitor->getSessionIds()->done(function () use (&$calledBack) {
            $calledBack = true;
        });

        $this->sessionStub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321]]);

        $this->assertTrue($listCalled && $calledBack);
    }

    public function test_list_event_getsessionid_callback_return_same_value()
    {

        $this->sessionStub->setSessionId(321);
        $listCalled = null;
        $calledBack = null;

        $this->monitor->on('list', function ($res) use (&$listCalled) {
            $listCalled = $res;
        });

        $this->monitor->getSessionIds()->done(function ($res) use (&$calledBack) {
            $calledBack = $res;
        });

        $this->sessionStub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[123, 456, 789]]);

        $this->assertSame($listCalled, $calledBack);
    }

    public function test_get_sessionids_removes_monitors_sessionid()
    {

        $this->sessionStub->setSessionId(321);
        $sessionIds = null;

        $this->monitor->getSessionIds()->done(function (array $ids) use (&$sessionIds) {
            $sessionIds = $ids;
        });

        $this->sessionStub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654, 987]]);

        $this->assertNotContains(321, $sessionIds);
    }

    public function test_second_get_sessionids_retrieves_ids_locally()
    {
        $this->sessionStub->setSessionId(123);
        
        $firstResult = null;
        $secondResult = null;

        $this->monitor->getSessionIds()->done(function (array $ids) use (&$firstResult) {
            $firstResult = $ids;
        });

        $this->sessionStub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654, 987]]);

        $this->monitor->getSessionIds()->done(function (array $ids) use (&$secondResult) {
            $secondResult = $ids;
        });

        $this->assertSame($firstResult, $secondResult);
    }

    public function test_session_registration_on_start()
    {
        $this->sessionStub->setSessionId(321);

        $this->monitor->start();
        $this->sessionStub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654]]);

        $this->assertTrue($this->monitor->hasSessionId(654));
    }

    // SESSION JOIN TESTS

    public function test_session_registration_on_join()
    {
        $this->sessionStub->setSessionId(321);

        $this->monitor->start();

        $sessionInfo = new stdClass();
        $sessionInfo->session = 654;

        $this->sessionStub->emit(SessionMonitor::SESSION_JOIN_TOPIC, [[$sessionInfo]]);

        $this->assertTrue($this->monitor->hasSessionId(654));
    }

    public function test_session_unregistration_on_leave()
    {
        $this->sessionStub->setSessionId(321);

        $this->monitor->start();
        $this->sessionStub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654]]);

        $this->sessionStub->emit(SessionMonitor::SESSION_LEAVE_TOPIC, [[654]]);

        $this->assertFalse($this->monitor->hasSessionId(654));
    }

    public function test_unknown_session_unregistration_causes_no_error()
    {
        $this->sessionStub->setSessionId(321);

        $this->monitor->start();

        $this->sessionStub->emit(SessionMonitor::SESSION_LEAVE_TOPIC, [[654]]);

        $this->assertFalse($this->monitor->hasSessionId(654));
    }

    public function test_session_join_emits_event()
    {
        $this->sessionStub->setSessionId(321);
        $this->monitor->start();
        $response = null;

        $this->monitor->on('join', function ($res) use (&$response) {
            $response = $res;
        });

        $sessionInfo = new stdClass();
        $sessionInfo->session = 654;

        $this->sessionStub->emit(SessionMonitor::SESSION_JOIN_TOPIC, [[$sessionInfo]]);

        $this->assertSame($sessionInfo, $response);
    }

    public function test_session_leave_emits_event()
    {
        $this->sessionStub->setSessionId(321);
        $this->monitor->start();
        $response = null;

        $this->sessionStub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654]]);

        $this->monitor->on('leave', function ($res) use (&$response) {
            $response = $res;
        });

        $this->sessionStub->emit(SessionMonitor::SESSION_LEAVE_TOPIC, [[654]]);

        $this->assertSame(654, $response);
    }

    // SESSION INFO TESTS

    public function test_get_sessioninfo_calls_promise()
    {
        $this->sessionStub->setSessionId(321);
        $response = null;

        $this->monitor->getSessionInfo(654)->done(function ($res) use (&$response) {
            $response = $res;
        });

        $sessionInfo = new stdClass();
        $sessionInfo->session = 654;

        $this->sessionStub->respondToCall(SessionMonitor::SESSION_INFO_TOPIC, $sessionInfo);

        $this->assertSame($sessionInfo, $response);
    }

    public function test_get_sessioninfo_calls_event()
    {
        $this->sessionStub->setSessionId(321);
        $response = null;

        $this->monitor->on('info', function ($res) use (&$response) {
            $response = $res;
        });

        $this->monitor->getSessionInfo(654);

        $sessionInfo = new stdClass();
        $sessionInfo->session = 654;

        $this->sessionStub->respondToCall(SessionMonitor::SESSION_INFO_TOPIC, $sessionInfo);

        $this->assertSame($sessionInfo, $response);
    }

    public function test_get_sessioninfo_fail_emits_event()
    {
        $this->sessionStub->setSessionId(321);
        $response = null;

        $this->monitor->on('error', function ($res) use (&$response) {
            $response = $res;
        });

        $this->monitor->getSessionInfo(654);

        $errorMessage = $this->getMockBuilder(Message\ErrorMessage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sessionStub->failCall(SessionMonitor::SESSION_INFO_TOPIC, $errorMessage);

        $this->assertSame($errorMessage, $response);
    }

    public function test_invalid_sessioninfo_does_not_get_added()
    {
        $this->sessionStub->setSessionId(321);

        $sessionInfo = new stdClass();
        $sessionInfo->id = 654;

        $this->monitor->start();

        $this->sessionStub->completeSubscription(SessionMonitor::SESSION_JOIN_TOPIC);

        $this->sessionStub->emit(SessionMonitor::SESSION_JOIN_TOPIC, [[$sessionInfo]]);

        $this->assertFalse($this->monitor->hasSessionId(654));
    }

    // STOP MONITOR TESTS

    public function test_is_not_running_after_stop()
    {
        $this->monitor->start();
        $this->sessionStub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654]]);
        $this->sessionStub->completeSubscription(SessionMonitor::SESSION_JOIN_TOPIC);
        $this->sessionStub->completeSubscription(SessionMonitor::SESSION_LEAVE_TOPIC);

        $this->monitor->stop();

        $this->assertFalse($this->monitor->isRunning());
    }

    public function test_stop_emits_event()
    {
        $this->monitor->start();
        $response = null;

        $this->monitor->on('stop', function ($res) use (&$response) {
            $response = $res;
        });

        $this->sessionStub->respondToCall(SessionMonitor::SESSION_LIST_TOPIC, [[321, 654]]);
        $this->sessionStub->completeSubscription(SessionMonitor::SESSION_JOIN_TOPIC);
        $this->sessionStub->completeSubscription(SessionMonitor::SESSION_LEAVE_TOPIC);

        $this->monitor->stop();

        $this->assertSame($this->monitor, $response);
    }
}
