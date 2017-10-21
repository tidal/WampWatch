<?php

/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Adapter;

/**
 * Interface Tidal\WampWatch\Adapter\AdapterInterface.
 *
 */
interface AdapterInterface
{
    /**
     * @return object
     */
    public function getAdaptee();
}
