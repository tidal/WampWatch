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

namespace Tidal\WampWatch\Model\Contract\Property;


interface ObjectCollectionInterface extends CollectionInterface
{
    /**
     * Set a constrain on a class or interface name the collection's items must be an instance of.
     * Paramater 1 $name expects a fully qualified class name.
     *
     * @param string $cls A Fully qualified class name.
     */
    public function setObjectConstrain($cls);
}