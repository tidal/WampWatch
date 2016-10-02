<?php
/**
 *
 *  * This file is part of the Tidal/WampWatch package.
 *  * (c) 2016 Timo Michna <timomichna/yahoo.de>
 *  *
 *  * For the full copyright and license information, please view the LICENSE
 *  * file that was distributed with this source code.
 *
 */

namespace tests\unit\Model;

use Tidal\WampWatch\Model\Procedure;
use Tidal\WampWatch\Model\Session;
use Tidal\WampWatch\Model\Registration;


class RegistrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Registration
     */
    private $registration;

    public function setUp()
    {
        $this->registration = new Registration(
            321,
            $this->getSessionMock(),
            $this->getProcedureMock()
        );
    }

    public function test_can_retrieve_id()
    {
        $this->assertSame(321, $this->registration->getId());
    }

    public function test_can_retrieve_session()
    {
        $this->assertSame('baz', $this->registration->getSession()->getId());
    }

    public function test_can_retrieve_topic()
    {
        $this->assertSame('foo', $this->registration->getProcedure()->getUri());
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
     * @return \PHPUnit_Framework_MockObject_MockObject|Procedure
     */
    private function getProcedureMock()
    {
        $mock = $this->getNoConstructorMock(Procedure::class);
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
