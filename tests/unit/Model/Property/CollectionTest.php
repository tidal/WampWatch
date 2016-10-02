<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\Unit\Model\Property;

use Tidal\WampWatch\Model\Property\Collection;
use PHPUnit_Framework_TestCase;
use InvalidArgumentException;

class CollectionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Collection
     */
    private $collection;

    public function setUp()
    {
        $this->collection = new Collection();
    }

    public function test_can_append()
    {
        $this->collection->append('foo');

        $this->assertEquals('foo', $this->collection[0]);
    }

    public function test_can_get_generator()
    {
        $values = ['foo', 'bar', 'baz'];

        foreach ($values as $value) {
            $this->collection->append($value);
        }

        foreach ($this->collection->getGenerator() as $key => $value) {
            $this->assertEquals($values[$key], $value);
        }
    }

    /**
     *
     */
    public function test_can_set_valid_callback()
    {
        $res = null;

        $this->collection->setValidationCallback(function ($value) use (&$res) {
            $res = $value;

            return true;
        });

        $this->collection->append('foo');

        $this->assertEquals('foo', $res);
    }

    /**
     *
     */
    public function test_failed_validation_callback_throws_exception()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $this->collection->setValidationCallback(function () {
            return false;
        });

        $this->collection->append('foo');
    }

    /**
     *
     */
    public function test_can_access_values()
    {
        $this->assertFalse($this->collection->has('foo'));
        $this->collection->set('foo', 'bar');
        $this->assertTrue($this->collection->has('foo'));
        $this->assertEquals('bar', $this->collection->get('foo'));
    }

    /**
     * @param string $type
     * @param mixed  $value
     *
     * @dataProvider retrieveValidItemTypes
     */
    public function test_can_set_valid_item_types($type, $value)
    {
        $this->collection->setItemType($type);

        $this->collection->append($value);

        $this->assertEquals($value, $this->collection[0]);
    }

    /**
     * @return array
     */
    public function retrieveValidItemTypes()
    {
        return [
            ['string', 'foo'],
            ['integer', 321],
            ['double', 3.21],
            ['boolean', true],
            ['array', []],
            ['object', new \stdClass()]
        ];
    }

    /**
     * @param string $type
     * @param mixed  $value
     *
     * @dataProvider retrieveInvalidItemTypes
     */
    public function test_cannot_set_invalid_item_types($type, $value)
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $this->collection->setItemType($type);

        $this->collection->append($value);
    }

    /**
     * @return array
     */
    public function retrieveInvalidItemTypes()
    {
        return [
            ['string', 321],
            ['string', 3.21],
            ['string', true],
            ['string', []],
            ['string', new \stdClass()],
            ['integer', '321'],
            ['integer', 3.21],
            ['integer', true],
            ['integer', []],
            ['integer', new \stdClass()],
            ['double', '321'],
            ['double', 321],
            ['double', true],
            ['double', []],
            ['boolean', '321'],
            ['boolean', 321],
            ['boolean', 3.21],
            ['boolean', []],
            ['boolean', new \stdClass()],
            ['array', 321],
            ['array', 3.21],
            ['array', '321'],
            ['array', true],
            ['array', new \stdClass()],
            ['object', 321],
            ['object', 3.21],
            ['object', true],
            ['object', '321'],
            ['object', []]
        ];
    }


}
