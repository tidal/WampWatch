<?php
/**
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\Unit\Model;

use Tidal\WampWatch\Model\Procedure;
use Tidal\WampWatch\Test\Unit\Stub\MockHelper;
use PHPUnit_Framework_TestCase as TestCase;

trait HasProcedureTrait
{
    /**
     * @param string $uri
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Procedure
     */
    private function getProcedureMock($uri = 'faa')
    {
        /** @var TestCase $self */
        $self = $this;
        $mock = MockHelper::getNoConstructorMock($self, Procedure::class);
        $mock->expects($this->any())
            ->method('getUri')
            ->willReturn($uri);

        return $mock;
    }
}
