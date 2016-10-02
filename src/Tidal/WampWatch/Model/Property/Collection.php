<?php
/**
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Model\Property;

use ArrayObject;
use InvalidArgumentException;
use Generator;
use Tidal\WampWatch\Model\Contract\Property\CollectionInterface;

class Collection extends ArrayObject implements CollectionInterface
{
    private $itemType;

    private $validationCallback;

    public function append($value)
    {
        $this->validate($value);

        parent::append($value);
    }

    private function validate($item)
    {
        $this->validateItemType($item);
        $this->validateCallback($item);
    }

    private function validateItemType($item)
    {
        if (isset($this->itemType) && $type = gettype($item) !== $this->itemType) {
            throw new InvalidArgumentException("Expected item of type '{$this->itemType}'. -> '$type' given.");
        }
    }

    private function validateCallback($item)
    {
        if (!isset($this->validationCallback)) {
            return;
        }

        $callback = $this->validationCallback;

        if (!$callback($item)) {
            throw new InvalidArgumentException('Item failed validation.');
        }
    }

    public function setItemType($type)
    {
        $this->itemType = (string)$type;
    }

    public function has($key)
    {
        return $this->offsetExists($key);
    }

    public function set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    public function offsetSet($index, $newValue)
    {
        $this->validate($newValue);

        parent::offsetSet($index, $newValue);
    }

    public function get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * @return Generator
     */
    public function getGenerator()
    {
        foreach ($this as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * Registers a callback function to validate new Collection entries.
     * The callback function must return a boolean value.
     * When the callback returns false the new item will not be added to the collection.
     *
     * @param callable $callback
     */
    public function setValidationCallback(callable $callback)
    {
        $this->validationCallback = $callback;
    }
}
