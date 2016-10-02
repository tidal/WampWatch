<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\Unit\Stub;

use Tidal\WampWatch\Model\Behavior\Property\HasCollectionsTrait;
use Tidal\WampWatch\Model\Contract\Property\CollectionInterface;

class HasCollectionsImplementation
{
    use HasCollectionsTrait;

    private $foo;

    private $bar;

    private $baz;

    public function init($name, CollectionInterface $collection)
    {
        $this->initCollection($name, $collection);
    }

    public function get($name)
    {
        return $this->getCollection($name);
    }

    public function append($name, $value)
    {
        $this->appendTo($name, $value);
    }

    public function has($name)
    {
        return $this->hasCollection($name);
    }


}