<?php
/**
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\unit\Model;

use Tidal\WampWatch\Model\Topic;
use Tidal\WampWatch\Test\Unit\Stub\MockHelper;
use PHPUnit_Framework_TestCase as TestCase;

trait HasTopicTrait
{
    /**
     * @param string $uri
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Topic
     */
    private function getTopicMock($uri = 'foo')
    {
        /** @var TestCase $self */
        $self = $this;
        $mock = MockHelper::getNoConstructorMock($self, Topic::class);
        $mock->expects($this->any())
            ->method('getUri')
            ->willReturn($uri);

        return $mock;
    }
}
