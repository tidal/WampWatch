<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Async\Adapter;

use Tidal\WampWatch\Async\PromiseFactoryInterface as PromiseFactory;
use Tidal\WampWatch\Async\PromiseInterface;

interface PromiseFactoryInterface extends PromiseFactory
{
    /**
     * @param object $adaptee
     *
     * @return PromiseInterface
     */
    public function createFromAdaptee($adaptee);
}
