<?php

require_once __DIR__.'/../bootstrap.php';


use Mockery as M;
use Tidal\WampWatch\Adapter\Thruway\ClientSession;
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

        $adapter = new ClientSession($session);
        $monitor = new SessionMonitor($adapter);
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

        // test 'wamp.session.on_join' was called.
        $session->shouldReceive('subscribe')->once()
            ->with('wamp.session.on_join', \Mockery::any(), \Mockery::any())
            ->andReturn($promise);
        // test 'wamp.session.on_leave' was called.
        $session->shouldReceive('subscribe')->once()
            ->with('wamp.session.on_leave', \Mockery::any(), \Mockery::any())
            ->andReturn($promise);

        $adapter = new ClientSession($session);
        $monitor = new SessionMonitor($adapter);
        $monitor->start();
    }

    public function test_start_calls_session_list()
    {
        $promise = M::mock('React\Promise\Promise');
        $promise->shouldReceive('then')->atLeast()->once();

        $session = M::mock('Thruway\ClientSession');
        $session->shouldReceive('subscribe')->once()
            ->andReturn($promise);
        $session->shouldReceive('subscribe')->once()
            ->andReturn($promise);

        // test 'wamp.session.list' was called.
        $session->shouldReceive('call')->once()
            ->with('wamp.session.list', \Mockery::any(), \Mockery::any(), \Mockery::any())
            ->andReturn($promise);

        $adapter = new ClientSession($session);
        $monitor = new SessionMonitor($adapter);
        $monitor->start();
    }




}
