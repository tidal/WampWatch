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

namespace Tidal\WampWatch\Test\Unit\Model;

use Tidal\WampWatch\Model\Session;
use Tidal\WampWatch\Test\Unit\Stub\MockHelper;
use PHPUnit_Framework_TestCase as TestCase;

trait HasSessionTrait
{
    /**
     * @param string $id
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Session
     */
    private function getSessionMock($id = 'baz')
    {
        /** @var TestCase $self */
        $self = $this;
        $mock = MockHelper::getNoConstructorMock($self, Session::class);
        $mock->expects($this->any())
            ->method('getId')
            ->willReturn($id);

        return $mock;
    }
}