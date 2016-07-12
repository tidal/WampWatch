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




}
