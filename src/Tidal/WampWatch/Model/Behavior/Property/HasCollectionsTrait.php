<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Model\Behavior\Property;

use InvalidArgumentException;
use Tidal\WampWatch\Model\Contract\Property\CollectionInterface;

trait HasCollectionsTrait
{
    private function initCollection($name, CollectionInterface $collection)
    {
        if (property_exists($this, $name)) {
            $this->{$name} = $collection;
        }
    }

    private function appendTo($name, $value)
    {
        $this->getCollection($name)->append($value);
    }

    /**
     * @param string $name the name of the collection
     *
     * @throws InvalidArgumentException when the collection has not been initialized before
     *
     * @return CollectionInterface the collection object
     */
    private function getCollection($name)
    {
        if (!$this->hasCollection($name)) {
            throw new InvalidArgumentException("No Collection with name '$name' registered.");
        }

        return $this->{$name};
    }

    /**
     * @param string $name
     */
    private function hasCollection($name)
    {
        return property_exists($this, $name) && is_a($this->{$name}, CollectionInterface::class);
    }
}
