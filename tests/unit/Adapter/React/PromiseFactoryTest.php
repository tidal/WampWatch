<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\Unit\Adapter\React;

use Tidal\WampWatch\Adapter\React\PromiseFactory;
use Tidal\WampWatch\Adapter\React\PromiseAdapter;
use React\Promise\Promise as ReactPromise;

class PromiseFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PromiseFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new PromiseFactory();
    }

    public function test_can_create()
    {
        $this->assertInstanceOf(PromiseAdapter::class, $this->factory->create(function () {
        }));
    }

    public function test_create_wraps_react_promise()
    {
        $this->assertInstanceOf(ReactPromise::class, $this->factory->create(function () {
        })->getAdaptee());
    }

    public function test_can_create_from_adaptee()
    {
        $this->assertInstanceOf(PromiseAdapter::class, $this->factory->createFromAdaptee(
            $this->getMockBuilder(ReactPromise::class)
                ->disableOriginalConstructor()
                ->getMock()
        ));
    }
}
