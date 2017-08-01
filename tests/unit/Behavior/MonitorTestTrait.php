<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\Unit\Behavior;

use Tidal\WampWatch\Stub\ClientSessionStub;

/**
 * Trait tests\unit\Behavior\MonitorTestTrait *
 */
trait MonitorTestTrait
{
    /**
     * @var ClientSessionStub
     */
    private $sessionStub;

    private function setUpSessionStub()
    {
        $this->sessionStub = new ClientSessionStub();
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

    /**
     * Returns a builder object to create mock objects using a fluent interface.
     *
     * @param string $className
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder
     */
    abstract public function getMockBuilder($className);
}