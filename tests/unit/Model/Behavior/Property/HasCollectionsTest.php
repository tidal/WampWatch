<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\unit\Model\Behavior\Property;

use Tidal\WampWatch\Model\Property\ObjectCollection;
use Tidal\WampWatch\Test\Unit\Stub\HasCollectionsImplementation;
use PHPUnit_Framework_TestCase;
use InvalidArgumentException;

class HasCollectionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var HasCollectionsImplementation
     */
    private $mock;

    public function setUp()
    {
        $this->mock = new HasCollectionsImplementation();
    }

    public function test_can_initialize_collection()
    {
        $collection = $this->getMockBuilder(ObjectCollection::class)
            ->getMock();

        $this->mock->init(
            'foo',
            $collection
        );

        $this->assertTrue($this->mock->has('foo'));
    }

    public function test_can_append_value_to_collection()
    {
        $collection = $this->getMockBuilder(ObjectCollection::class)
            ->getMock();

        $collection
            ->expects($this->once())
            ->method('append')
            ->with('bar');

        $this->mock->init(
            'foo',
            $collection
        );
        $this->mock->append('foo', 'bar');
    }

    public function test_can_retrieve_collection()
    {
        $collection = $this->getMockBuilder(ObjectCollection::class)
            ->getMock();

        $this->mock->init(
            'foo',
            $collection
        );

        $this->assertEquals($collection, $this->mock->get('foo'));
    }

    public function test_retrieve_throws_exception_on_unknown_collection()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $this->mock->get('foo');
    }
}
