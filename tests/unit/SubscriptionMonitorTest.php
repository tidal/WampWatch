<?php
/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Test\unit;

require_once __DIR__ . '/../bootstrap.php';

use Tidal\WampWatch\SubscriptionMonitor;
use Tidal\WampWatch\Stub\ClientSessionStub;
use Thruway\CallResult;
use Thruway\Message\ResultMessage;

class SubscriptionMonitorTest extends \PHPUnit_Framework_TestCase
{
    public function test_starts_returns_true()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);

        $res = $monitor->start();

        $this->assertEquals(true, $res);
    }

    public function test_is_not_running_before_started()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);

        $this->assertFalse($monitor->isRunning());
    }

    public function test_is_not_running_before_oncreate_subscription()
    {
        $stub = new ClientSessionStub();
        $subIdMap = $this->getSubscriptionIdMap();
        $monitor = new SubscriptionMonitor($stub);
        $monitor->start();

        $stub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $this->assertFalse($monitor->isRunning());
    }

    public function test_is_not_running_before_ondelete_subscription()
    {
        $stub = new ClientSessionStub();
        $subIdMap = $this->getSubscriptionIdMap();
        $monitor = new SubscriptionMonitor($stub);
        $monitor->start();

        $stub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $this->assertFalse($monitor->isRunning());
    }

    public function test_is_not_running_before_onsubscribe_subscription()
    {
        $stub = new ClientSessionStub();
        $subIdMap = $this->getSubscriptionIdMap();
        $monitor = new SubscriptionMonitor($stub);
        $monitor->start();

        $stub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $this->assertFalse($monitor->isRunning());
    }

    public function test_is_not_running_before_onunsubscribe_subscription()
    {
        $stub = new ClientSessionStub();
        $subIdMap = $this->getSubscriptionIdMap();
        $monitor = new SubscriptionMonitor($stub);
        $monitor->start();

        $stub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);

        $this->assertFalse($monitor->isRunning());
    }

    public function test_is_not_running_before_list_response()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);
        $monitor->start();

        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $this->assertFalse($monitor->isRunning());
    }

    public function test_is_running_after_subscriptions_and_list()
    {
        $stub = new ClientSessionStub();
        $subIdMap = $this->getSubscriptionIdMap();
        $monitor = new SubscriptionMonitor($stub);
        $monitor->start();

        $stub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $this->assertTrue($monitor->isRunning());
    }

    public function test_start_event_after_running()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);
        $subIdMap = $this->getCallResultMock();
        $stub->setSessionId(321);
        $response = null;

        $monitor->on('start', function ($res) use (&$response) {
            $response = $res;
        });

        $monitor->start();

        $stub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $this->assertEquals($subIdMap->getResultMessage()->getArguments()[0], $response);
    }

    // META SUBSCRIPTION AND CALL TESTS

    public function test_start_subscribes_to_create_topic()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);
        $monitor->start();

        $this->assertTrue(
            $stub->hasSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC)
        );
    }

    public function test_start_subscribes_to_delete_topic()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);
        $monitor->start();

        $this->assertTrue(
            $stub->hasSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC)
        );
    }

    public function test_start_subscribes_to_subscribe_topic()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);
        $monitor->start();

        $this->assertTrue(
            $stub->hasSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC)
        );
    }

    public function test_start_subscribes_to_unsubscribe_topic()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);
        $monitor->start();

        $this->assertTrue(
            $stub->hasSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC)
        );
    }

    public function test_start_calls_session_list()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);
        $monitor->start();

        $this->assertTrue(
            $stub->hasCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC)
        );
    }

    // SUBSCRIPTION INFO TESTS

    public function test_get_subscription_info_emits_event()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);
        $info = $this->getSubscriptionInfo();
        $res = null;

        $monitor->on('info', function ($r) use (&$res) {
            $res = $r;
        });

        $monitor->getSubscriptionInfo(321);

        $stub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_GET_TOPIC, $info);

        $this->assertSame($info, $res);
    }

    public function test_get_subscription_info_calls_promise()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);
        $info = $this->getSubscriptionInfo();
        $res = null;

        $monitor->getSubscriptionInfo(321)->done(function ($r) use (&$res) {
            $res = $r;
        });

        $stub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_GET_TOPIC, $info);

        $this->assertSame($info, $res);
    }

    // SUBSCRIPTION EVENT TESTS

    public function test_create_event()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);
        $info = [321, $this->getSubscriptionInfo()];
        $subIdMap = $this->getSubscriptionIdMap();
        $res = null;

        $monitor->start();

        $stub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $monitor->on('create', function ($sessionId, $subscriptionInfo) use (&$res) {
            $res = [$sessionId, $subscriptionInfo];
        });

        $stub->emit(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC, [$info]);

        $this->assertSame($info, $res);
    }

    public function test_delete_event()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);
        $subIdMap = $this->getSubscriptionIdMap();
        $info = [321, 654];
        $res = null;

        $monitor->start();

        $stub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $monitor->on('delete', function ($sessionId, $subscriptionId) use (&$res) {
            $res = [$sessionId, $subscriptionId];
        });

        $stub->emit(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC, [$info]);

        $this->assertSame($info, $res);
    }

    public function test_subscribe_event()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);
        $subIdMap = $this->getSubscriptionIdMap();
        $info = [321, 654];
        $res = null;

        $monitor->start();

        $stub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $monitor->on('subscribe', function ($sessionId, $subscriptionId) use (&$res) {
            $res = [$sessionId, $subscriptionId];
        });

        $stub->emit(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC, [$info]);

        $this->assertSame($info, $res);
    }

    public function test_unsubscribe_event()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);
        $subIdMap = $this->getSubscriptionIdMap();
        $info = [321, 654];
        $res = null;

        $monitor->start();

        $stub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $stub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $monitor->on('unsubscribe', function ($sessionId, $subscriptionId) use (&$res) {
            $res = [$sessionId, $subscriptionId];
        });

        $stub->emit(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC, [$info]);

        $this->assertSame($info, $res);
    }

    // SUBSCRIPTION DETAIL TESTS

    public function test_get_subscription_ids_returns_subscription_map()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);
        $subIdMap = $this->getSubscriptionIdMap();
        $callResult = $this->getCallResultMock();
        $res = null;

        $monitor->getSubscriptionIds()->done(function ($r) use (&$res) {
            $res = $r;
        });

        $stub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $callResult);

        $this->assertEquals($subIdMap, $res);
    }

    public function test_2nd_get_subscription_ids_returns_subscription_map_locally()
    {
        $stub = new ClientSessionStub();
        $monitor = new SubscriptionMonitor($stub);
        $subIdMap = $this->getSubscriptionIdMap();
        $first = null;
        $second = null;

        $monitor->getSubscriptionIds()->done(function ($r) use (&$first) {
            $first = $r;
        });

        $stub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);

        $monitor->getSubscriptionIds()->done(function ($r) use (&$second) {
            $second = $r;
        });

        $this->assertEquals($first, $second);
    }

    private function getSubscriptionInfo()
    {
        return [
            'id'      => 321,
            'created' => '1999-09-09T09:09:09.999Z',
            'uri'     => 'com.example.topic',
            'match'   => 'exact',
        ];
    }

    private function getSubscriptionIdMap()
    {
        return json_decode('{"exact": [321], "prefix": [654], "wildcard": [987]}');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|CallResult
     */
    private function getCallResultMock()
    {
        $mock = $this->getMockBuilder(CallResult::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->any())
            ->method('getResultMessage')
            ->willReturn(
                $this->getResultMessageMock()
            );

        return $mock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|
     */
    private function getResultMessageMock()
    {
        $mock = $this->getMockBuilder(ResultMessage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->any())
            ->method('getArguments')
            ->willReturn(
                [
                    $this->getSubscriptionIdMap(),
                ]
            );

        return $mock;
    }
}
