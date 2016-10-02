<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\Unit\Model;

use Tidal\WampWatch\Model\Realm;
use Tidal\WampWatch\Test\Unit\Stub\MockHelper;
use PHPUnit_Framework_TestCase as TestCase;

trait HasRealmTrait
{
    /**
     * @param string $name
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Realm
     */
    private function getRealmMock($name = 'faz')
    {
        /** @var TestCase $self */
        $self = $this;
        $mock = MockHelper::getNoConstructorMock($self, Realm::class);
        $mock->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        return $mock;
    }
}