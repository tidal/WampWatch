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

namespace Tidal\WampWatch\Model\EventSourcing\ValueObject;

use ArrayObject;
use InvalidArgumentException;

class ValueObject extends ArrayObject implements ValueObjectInterface
{
    use ValueObjectTrait;

    public function __construct($input, $flags = 0, $iterator_class = "ArrayIterator")
    {
        parent::__construct($input, $flags, $iterator_class);

        if (is_array($input)) {
            $this->init($input);
        }
    }

    public static function create($input)
    {
        return new self($input);
    }

}