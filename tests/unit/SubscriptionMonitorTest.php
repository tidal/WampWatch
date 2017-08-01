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

use Tidal\WampWatch\SubscriptionMonitor;
use Tidal\WampWatch\Stub\ClientSessionStub;
use Thruway\CallResult;
use Thruway\Message\ResultMessage;
use Tidal\WampWatch\Test\Unit\Behavior\MonitorTestTrait;

class SubscriptionMonitorTest extends \PHPUnit_Framework_TestCase
{
    use MonitorTestTrait;

    /**
     * @var ClientSessionStub
     */
    private $sessionStub;

    /**
     * @var SubscriptionMonitor
     */
    private $monitor;

    public function setUp()
    {
        $this->setUpSessionStub();
        $this->monitor = new SubscriptionMonitor($this->sessionStub);
    }
    
    public function test_starts_returns_true()
    {
        $res = $this->monitor->start();

        $this->assertEquals(true, $res);
    }

    public function test_is_not_running_before_started()
    {
        $this->assertFalse($this->monitor->isRunning());
    }

    public function test_is_not_running_before_oncreate_subscription()
    {
        $subIdMap = $this->getSubscriptionIdMap();
        $this->monitor->start();

        $this->sessionStub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $this->assertFalse($this->monitor->isRunning());
    }

    public function test_is_not_running_before_ondelete_subscription()
    {
        $subIdMap = $this->getSubscriptionIdMap();

        $this->monitor->start();

        $this->sessionStub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $this->assertFalse($this->monitor->isRunning());
    }

    public function test_is_not_running_before_onsubscribe_subscription()
    {

        $subIdMap = $this->getSubscriptionIdMap();
        $this->monitor->start();

        $this->sessionStub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $this->assertFalse($this->monitor->isRunning());
    }

    public function test_is_not_running_before_onunsubscribe_subscription()
    {
        $subIdMap = $this->getSubscriptionIdMap();

        $this->monitor->start();

        $this->sessionStub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);

        $this->assertFalse($this->monitor->isRunning());
    }

    public function test_is_not_running_before_list_response()
    {
        $this->monitor->start();

        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $this->assertFalse($this->monitor->isRunning());
    }

    public function test_is_running_after_subscriptions_and_list()
    {
        $subIdMap = $this->getSubscriptionIdMap();
        $this->monitor->start();

        $this->sessionStub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $this->assertTrue($this->monitor->isRunning());
    }

    public function test_start_event_after_running()
    {
        $subIdMap = $this->getCallResultMock();
        $this->sessionStub->setSessionId(321);
        $response = null;

        $this->monitor->on('start', function ($res) use (&$response) {
            $response = $res;
        });

        $this->monitor->start();

        $this->sessionStub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $this->assertEquals($subIdMap->getResultMessage()->getArguments()[0], $response);
    }

    // META SUBSCRIPTION AND CALL TESTS

    public function test_start_subscribes_to_create_topic()
    {
        $this->monitor->start();

        $this->assertTrue(
            $this->sessionStub->hasSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC)
        );
    }

    public function test_start_subscribes_to_delete_topic()
    {
        $this->monitor->start();

        $this->assertTrue(
            $this->sessionStub->hasSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC)
        );
    }

    public function test_start_subscribes_to_subscribe_topic()
    {
        $this->monitor->start();

        $this->assertTrue(
            $this->sessionStub->hasSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC)
        );
    }

    public function test_start_subscribes_to_unsubscribe_topic()
    {
        $this->monitor->start();

        $this->assertTrue(
            $this->sessionStub->hasSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC)
        );
    }

    public function test_start_calls_session_list()
    {
        $this->monitor->start();

        $this->assertTrue(
            $this->sessionStub->hasCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC)
        );
    }

    // SUBSCRIPTION INFO TESTS

    public function test_get_subscription_info_emits_event()
    {
        $info = $this->getSubscriptionInfo();
        $res = null;

        $this->monitor->on('info', function ($r) use (&$res) {
            $res = $r;
        });

        $this->monitor->getSubscriptionInfo(321);

        $this->sessionStub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_GET_TOPIC, $info);

        $this->assertSame($info, $res);
    }

    public function test_get_subscription_info_calls_promise()
    {
        $info = $this->getSubscriptionInfo();
        $res = null;

        $this->monitor->getSubscriptionInfo(321)->done(function ($r) use (&$res) {
            $res = $r;
        });

        $this->sessionStub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_GET_TOPIC, $info);

        $this->assertSame($info, $res);
    }

    // SUBSCRIPTION EVENT TESTS

    public function test_create_event()
    {
        $info = [321, $this->getSubscriptionInfo()];
        $subIdMap = $this->getSubscriptionIdMap();
        $res = null;

        $this->monitor->start();

        $this->sessionStub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $this->monitor->on('create', function ($sessionId, $subscriptionInfo) use (&$res) {
            $res = [$sessionId, $subscriptionInfo];
        });

        $this->sessionStub->emit(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC, [$info]);

        $this->assertSame($info, $res);
    }

    public function test_delete_event()
    {
        $subIdMap = $this->getSubscriptionIdMap();
        $info = [321, 654];
        $res = null;

        $this->monitor->start();

        $this->sessionStub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $this->monitor->on('delete', function ($sessionId, $subscriptionId) use (&$res) {
            $res = [$sessionId, $subscriptionId];
        });

        $this->sessionStub->emit(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC, [$info]);

        $this->assertSame($info, $res);
    }

    public function test_subscribe_event()
    {
        $subIdMap = $this->getSubscriptionIdMap();
        $info = [321, 654];
        $res = null;

        $this->monitor->start();

        $this->sessionStub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $this->monitor->on('subscribe', function ($sessionId, $subscriptionId) use (&$res) {
            $res = [$sessionId, $subscriptionId];
        });

        $this->sessionStub->emit(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC, [$info]);

        $this->assertSame($info, $res);
    }

    public function test_unsubscribe_event()
    {
        $subIdMap = $this->getSubscriptionIdMap();
        $info = [321, 654];
        $res = null;

        $this->monitor->start();

        $this->sessionStub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_DELETE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_CREATE_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_SUB_TOPIC);
        $this->sessionStub->completeSubscription(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC);

        $this->monitor->on('unsubscribe', function ($sessionId, $subscriptionId) use (&$res) {
            $res = [$sessionId, $subscriptionId];
        });

        $this->sessionStub->emit(SubscriptionMonitor::SUBSCRIPTION_UNSUB_TOPIC, [$info]);

        $this->assertSame($info, $res);
    }

    // SUBSCRIPTION DETAIL TESTS

    public function test_get_subscription_ids_returns_subscription_map()
    {
        $subIdMap = $this->getSubscriptionIdMap();
        $callResult = $this->getCallResultMock();
        $res = null;

        $this->monitor->getSubscriptionIds()->done(function ($r) use (&$res) {
            $res = $r;
        });

        $this->sessionStub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $callResult);

        $this->assertEquals($subIdMap, $res);
    }

    public function test_2nd_get_subscription_ids_returns_subscription_map_locally()
    {
        $subIdMap = $this->getSubscriptionIdMap();
        $first = null;
        $second = null;

        $this->monitor->getSubscriptionIds()->done(function ($r) use (&$first) {
            $first = $r;
        });

        $this->sessionStub->respondToCall(SubscriptionMonitor::SUBSCRIPTION_LIST_TOPIC, $subIdMap);

        $this->monitor->getSubscriptionIds()->done(function ($r) use (&$second) {
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
