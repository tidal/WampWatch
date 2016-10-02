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


use RuntimeException;

trait ValueObjectTrait
{
    private $initialized = false;

    public function __set($name, $value)
    {
        if ($this->isInitialized()) {
            throw new RuntimeException("Property '$name' is readonly");
        }

        $this->{$name} = $value;
    }

    private function isInitialized()
    {
        return $this->initialized;
    }

    private function setInitialized()
    {
        $this->initialized = true;
    }

    private function init($input)
    {
        if (!self::isCompound($input)) {
            $type = gettype($input);
            throw new InvalidArgumentException("First constructor argument expects object or array. ($type) given.");
        }

        foreach ($input as $key => $value) {
            $this->{$key} = self::isCompound($value)
                ? new self($value)
                : $value;
        }

        $this->initialized = true;
    }

    private static function isCompound($input)
    {
        return is_array($input) || is_object($input);
    }
}