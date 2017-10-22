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
use Thruway\CallResult;
use Thruway\Message\ResultMessage;
use Tidal\WampWatch\Async\PromiseInterface;
use Tidal\WampWatch\Async\Adapter\PromiseFactoryInterface;
use Tidal\WampWatch\Async\DeferredInterface;

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

    private function getResultInfo()
    {
        return [
            'id' => 321,
            'created' => '1999-09-09T09:09:09.999Z',
            'uri' => 'com.example.topic',
            'match' => 'exact',
        ];
    }

    private function createPromiseFactoryMock()
    {
        $mock = $this->getMockBuilder(PromiseFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->any())
            ->method('create')
            ->willReturn(
                $this->createPromiseMock()
            );

        return $mock;
    }

    private function createPromiseMock()
    {
        return $this->getMockBuilder(PromiseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

    }

    private function createDeferredFactoryMock()
    {
        $mock = $this->getMockBuilder(PromiseFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->any())
            ->method('create')
            ->willReturn(
                $this->createDeferrdMock()
            );

        return $mock;
    }

    private function createDeferrdMock()
    {
        return $this->getMockBuilder(DeferredInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

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