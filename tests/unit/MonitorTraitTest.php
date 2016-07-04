<?php

require_once __DIR__.'/../bootstrap.php';
require_once __DIR__.'/stub/MonitorTraitImplementation.php';

use Mockery as M;

/**
 * @author Timo Michna <timomichna@yahoo.de>
 */
class MonitorTraitTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->mock = M::mock('\Thruway\ClientSession');
        $this->monitorStub = new MonitorTraitImplementation($this->mock);
        $this->lastEvent = '';
        $this->lastArgs = [];
    }

    public function test_get_server_session()
    {
        $this->assertEquals($this->mock, $this->monitorStub->getServerSession());
    }

    public function test_start()
    {
        $testCase = $this;
        $this->monitorStub->on('start', function ($list) use ($testCase) {
            $testCase->lastEvent = 'start';
            $testCase->lastArgs = func_get_args();
        });

        $res = $this->monitorStub->start();

        $this->assertSame(true, $res);
        $this->assertSame('start', $this->lastEvent);
        $this->assertSame([], $this->lastArgs[0]);
        $this->assertSame(true, $this->monitorStub->isRunning(), 'monitor should be running, when it was started');
    }

    public function test_stop_without_being_started()
    {
        $testCase = $this;
        $this->monitorStub->on('stop', function ($list) use ($testCase) {
            $testCase->lastEvent = 'stop';
            $testCase->lastArgs = func_get_args();
        });
        $res = $this->monitorStub->stop();

        $this->assertSame(false, $res, "'stop' should return false, when monitor was not started");
        $this->assertSame('', $this->lastEvent, "'stop' event should not trigger, when monitor was not started");
    }

    public function test_stop_with_being_started()
    {
        $testCase = $this;
        $this->monitorStub->on('stop', function ($list) use ($testCase) {
            $testCase->lastEvent = 'stop';
            $testCase->lastArgs = func_get_args();
        });

        $this->monitorStub->start();
        $res = $this->monitorStub->stop();

        $this->assertSame(true, $res, "'stop' should return true, when monitor was stopped");
        $this->assertSame('stop', $this->lastEvent, "'stop' event should not trigger, when monitor was not started");
        $this->assertSame($this->monitorStub, $this->lastArgs[0]);
        $this->assertSame(false, $this->monitorStub->isRunning(), 'monitor should not be running, when it was stopped');
    }
}
