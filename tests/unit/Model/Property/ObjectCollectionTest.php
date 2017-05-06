<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\Unit\Model\Property;

use Tidal\WampWatch\Model\Property\ObjectCollection;
use PHPUnit_Framework_TestCase;
use InvalidArgumentException;

class ObjectCollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectCollection
     */
    private $collection;

    public function setUp()
    {
        $this->collection = new ObjectCollection();
    }

    /**
     * @param mixed $value
     *
     * @dataProvider retrieveTypes
     */
    public function test_can_append_all_values_without_constrain($value)
    {
        $this->collection->append($value);

        $this->assertEquals($value, $this->collection[0]);
    }

    /**
     * @return array
     */
    public function retrieveTypes()
    {
        return [
            ['foo'],
            [321],
            [3.21],
            [true],
            [[]],
            [new \stdClass()],
        ];
    }

    public function test_invalid_class_constrain_throws_exception()
    {
        $this->setExpectedException(
            InvalidArgumentException::class
        );

        $this->collection->setObjectConstrain('Foo');
    }

    public function test_invalid_class_value_throws_exception()
    {
        $this->setExpectedException(
            InvalidArgumentException::class
        );

        $this->collection->setObjectConstrain(\stdClass::class);

        $this->collection->append('foo');
    }

    public function test_given_class_is_valid()
    {
        $this->collection->setObjectConstrain(\stdClass::class);

        $value = new \stdClass();
        $this->collection->append($value);

        $this->assertEquals($value, $this->collection[0]);
    }
}
