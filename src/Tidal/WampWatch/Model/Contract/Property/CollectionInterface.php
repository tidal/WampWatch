<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Model\Contract\Property;

use IteratorAggregate;
use ArrayAccess;
use Serializable;
use Countable;

interface CollectionInterface extends IteratorAggregate, ArrayAccess, Serializable, Countable
{
    /**
     * Sets a property type constrain for collection items.
     *
     * @param $type
     */
    public function setItemType($type);

    /**
     * Registers a callback function to validate new Collection entries.
     * The callback function must return a boolean value.
     *
     *
     * @param callable $callback
     */
    public function setValidationCallback(callable $callback);

    /**
     * If the collection has an item with the given key.
     *
     * @param string $key the name of the key
     *
     * @return bool
     */
    public function has($key);

    /**
     * set an item with the given key.
     *
     * @param string $key   the name of the key
     * @param mixed  $value the item to add
     *
     * @return mixed
     */
    public function set($key, $value);

    /**
     * Retrieves an item with the given key.
     *
     * @param string $key the name of the key
     *
     * @return mixed
     */
    public function get($key);
}
