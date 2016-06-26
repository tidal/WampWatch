<?php

require_once __DIR__ . '/../bootstrap.php';
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

    public function test_start() {
        $this->session = $this->getMockBuilder('\Thruway\ClientSession')
                ->disableOriginalConstructor()
                ->getMock();
        $futureResult = new \React\Promise\Deferred();
        $this->session->expects($this->any())
            ->method('call')
            ->will(
                $this->returnValue($futureResult->promise())
            );
        
        $this->monitor = new SessionMonitor($this->session);
        $this->monitor->start();
        
       
        
    }
    
  
}
