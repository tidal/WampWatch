<?php

require_once __DIR__.'/../bootstrap.php';
//require_once __DIR__ . '/stub/ClientSessionStub.php';

use Mockery as M;
use Tidal\WampWatch\SessionMonitor;

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
        $promise = M::mock('React\Promise\Promise');
        $promise->shouldReceive('then')->atLeast()->once();

        $session = M::mock('Thruway\ClientSession');
        $session->shouldReceive('subscribe')->twice()
            ->andReturn($promise);
        $session->shouldReceive('call')->once()
            ->andReturn($promise);

        $monitor = new SessionMonitor($session);
        $res = $monitor->start();

        $this->assertEquals(true, $res);
    }

    public function test_start_subscribes_to_session()
    {
        $promise = M::mock('React\Promise\Promise');
        $promise->shouldReceive('then')->atLeast()->once();

        $session = M::mock('Thruway\ClientSession');
        $session->shouldReceive('call')->once()
            ->andReturn($promise);
        $session->shouldReceive('subscribe')->once()
            ->with('wamp.session.on_join', \Mockery::any())
            ->andReturn($promise);
        $session->shouldReceive('subscribe')->once()
            ->with('wamp.session.on_leave', \Mockery::any())
            ->andReturn($promise);
        $monitor = M::mock('Tidal\WampWatch\SessionMonitor', [$session])
            ->makePartial();

        $monitor->start();
    }
}
