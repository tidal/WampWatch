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

namespace Tidal\WampWatch\Async;


interface DeferredInterface
{
    /**
     * @return PromiseInterface
     */
    public function promise();

    /**
     * @param null $value
     */
    public function resolve($value = null);

    /**
     * @param null $reason
     */
    public function reject($reason = null);

    /**
     * @param null $update
     */
    public function notify($update = null);
}