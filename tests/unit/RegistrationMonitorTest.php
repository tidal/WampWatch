<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace tests\unit;

require_once __DIR__ . '/../bootstrap.php';

use Tidal\WampWatch\RegistrationMonitor;
use Tidal\WampWatch\Test\Unit\Behavior\MonitorTestTrait;

/**
 * Class tests\unit\RegistrationMonitorTest *
 */
class RegistrationMonitorTest extends \PHPUnit_Framework_TestCase
{
    use MonitorTestTrait;

    /**
     * @var RegistrationMonitor
     */
    private $monitor;

    public function setup()
    {
        $this->setUpSessionStub();
        $this->monitor = new RegistrationMonitor($this->sessionStub);
    }

    public function test_can_create_instance()
    {
        $this->assertInstanceOf(
            RegistrationMonitor::class,
            $this->monitor
        );
    }

    // STARTUP TESTS

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

        $this->sessionStub->respondToCall(RegistrationMonitor::REGISTRATION_LIST_TOPIC, $subIdMap);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_DELETE_TOPIC);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_REG_TOPIC);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_UNREG_TOPIC);

        $this->assertFalse($this->monitor->isRunning());
    }

    public function test_is_not_running_before_ondelete_subscription()
    {
        $subIdMap = $this->getSubscriptionIdMap();

        $this->monitor->start();

        $this->sessionStub->respondToCall(RegistrationMonitor::REGISTRATION_LIST_TOPIC, $subIdMap);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_CREATE_TOPIC);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_REG_TOPIC);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_UNREG_TOPIC);

        $this->assertFalse($this->monitor->isRunning());
    }

    public function test_is_not_running_before_onsubscribe_subscription()
    {

        $subIdMap = $this->getSubscriptionIdMap();

        $this->monitor->start();

        $this->sessionStub->respondToCall(RegistrationMonitor::REGISTRATION_LIST_TOPIC, $subIdMap);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_DELETE_TOPIC);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_CREATE_TOPIC);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_UNREG_TOPIC);

        $this->assertFalse($this->monitor->isRunning());
    }

    public function test_is_not_running_before_list_response()
    {
        $this->monitor->start();

        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_DELETE_TOPIC);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_CREATE_TOPIC);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_REG_TOPIC);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_UNREG_TOPIC);

        $this->assertFalse($this->monitor->isRunning());
    }

    public function test_is_running_after_subscriptions_and_list()
    {
        $this->startMonitor();

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

        $this->sessionStub->respondToCall(
            RegistrationMonitor::REGISTRATION_LIST_TOPIC,
            $subIdMap
        );
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_DELETE_TOPIC);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_CREATE_TOPIC);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_REG_TOPIC);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_UNREG_TOPIC);

        $this->assertEquals($subIdMap->getResultMessage()->getArguments()[0], $response);
    }

    // META SUBSCRIPTION AND CALL TESTS

    public function test_start_subscribes_to_create_topic()
    {
        $this->monitor->start();

        $this->assertTrue(
            $this->sessionStub->hasSubscription(RegistrationMonitor::REGISTRATION_CREATE_TOPIC)
        );
    }

    public function test_start_subscribes_to_delete_topic()
    {
        $this->monitor->start();

        $this->assertTrue(
            $this->sessionStub->hasSubscription(RegistrationMonitor::REGISTRATION_DELETE_TOPIC)
        );
    }

    public function test_start_subscribes_to_subscribe_topic()
    {
        $this->monitor->start();

        $this->assertTrue(
            $this->sessionStub->hasSubscription(RegistrationMonitor::REGISTRATION_REG_TOPIC)
        );
    }

    public function test_start_subscribes_to_unsubscribe_topic()
    {
        $this->monitor->start();

        $this->assertTrue(
            $this->sessionStub->hasSubscription(RegistrationMonitor::REGISTRATION_UNREG_TOPIC)
        );
    }

    public function test_start_calls_session_list()
    {
        $this->monitor->start();

        $this->assertTrue(
            $this->sessionStub->hasCall(RegistrationMonitor::REGISTRATION_LIST_TOPIC)
        );
    }

    // REGISTRATION EVENT TESTS

    public function test_create_event()
    {
        $info = [321, $this->getResultInfo()];
        $res = null;

        $this->startMonitor();

        $this->monitor->on('create', function ($sessionId, $subscriptionInfo) use (&$res) {
            $res = [$sessionId, $subscriptionInfo];
        });

        $this->sessionStub->emit(RegistrationMonitor::REGISTRATION_CREATE_TOPIC, [$info]);

        $this->assertSame($info, $res);
    }

    public function test_delete_event()
    {
        $info = [321, 654];
        $res = null;

        $this->startMonitor();

        $this->monitor->on('delete', function ($sessionId, $subscriptionId) use (&$res) {
            $res = [$sessionId, $subscriptionId];
        });

        $this->sessionStub->emit(RegistrationMonitor::REGISTRATION_DELETE_TOPIC, [$info]);

        $this->assertSame($info, $res);
    }

    private function startMonitor()
    {
        $this->monitor->start();
        $this->respondToStartupCalls();
    }

    private function respondToStartupCalls()
    {
        $this->sessionStub->respondToCall(
            RegistrationMonitor::REGISTRATION_LIST_TOPIC,
            $this->getSubscriptionIdMap()
        );
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_DELETE_TOPIC);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_CREATE_TOPIC);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_REG_TOPIC);
        $this->sessionStub->completeSubscription(RegistrationMonitor::REGISTRATION_UNREG_TOPIC);
    }
}
