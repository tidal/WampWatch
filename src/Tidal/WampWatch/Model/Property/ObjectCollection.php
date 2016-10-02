<?php
/**
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Model\Property;

use InvalidArgumentException;
use Tidal\WampWatch\Model\Contract\Property\ObjectCollectionInterface;

class ObjectCollection extends Collection implements ObjectCollectionInterface
{
    private $objectConstrain;

    /**
     * Set a constrain on a class or interface name the collection's items must be an instance of.
     * Paramater 1 $cls expects a fully qualified class name.
     *
     * @param string $cls A Fully qualified class name
     */
    public function setObjectConstrain($cls)
    {
        if (!class_exists($cls) && !interface_exists($cls)) {
            throw new InvalidArgumentException("Class or Interface '$cls' not found.");
        }

        $this->objectConstrain = $cls;
    }

    public function append($value)
    {
        $this->validateObjectConstrain($value);

        parent::append($value);
    }

    private function validateObjectConstrain($item)
    {
        if (!isset($this->objectConstrain)) {
            return;
        }

        if (!is_a($item, $this->objectConstrain)) {
            throw new InvalidArgumentException("Item must be instance of '{$this->objectConstrain}'");
        }
    }

    public function offsetSet($index, $newValue)
    {
        $this->validateObjectConstrain($newValue);

        parent::offsetSet($index, $newValue);
    }
}
