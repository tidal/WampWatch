<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Contract\Async;

interface PromiseInterface
{
    /**
     * @return PromiseInterface
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null, callable $onProgress = null);

    public function done(callable $onFulfilled = null, callable $onRejected = null, callable $onProgress = null);

    /**
     * @return PromiseInterface
     */
    public function otherwise(callable $onRejected);

    /**
     * @return PromiseInterface
     */
    public function always(callable $onFulfilledOrRejected);

    /**
     * @return PromiseInterface
     */
    public function progress(callable $onProgress);
}
