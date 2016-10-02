<?php
/**
 *
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace tests\unit\Model;

use Tidal\WampWatch\Model\Topic;
use Tidal\WampWatch\Model\Session;
use Tidal\WampWatch\Model\Subscription;


class SubscriptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Subscription
     */
    private $subscription;

    public function setUp()
    {
        $this->subscription = new Subscription(
            321,
            $this->getSessionMock(),
            $this->getTopicMock()
        );
    }

    public function test_can_retrieve_id()
    {
        $this->assertSame(321, $this->subscription->getId());
    }

    public function test_can_retrieve_session()
    {
        $this->assertSame('baz', $this->subscription->getSession()->getId());
    }

    public function test_can_retrieve_topic()
    {
        $this->assertSame('foo', $this->subscription->getTopic()->getUri());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Session
     */
    private function getSessionMock()
    {
        $mock = $this->getNoConstructorMock(Session::class);
        $mock->expects($this->any())
            ->method('getId')
            ->willReturn('baz');

        return $mock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Topic
     */
    private function getTopicMock()
    {
        $mock = $this->getNoConstructorMock(Topic::class);
        $mock->expects($this->any())
            ->method('getUri')
            ->willReturn('foo');

        return $mock;
    }

    /**
     * @param string $class
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getNoConstructorMock($class)
    {
        return $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
