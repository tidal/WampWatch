<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Async;

interface PromiseInterface
{
    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     * @param callable|null $onProgress
     *
     * @return PromiseInterface
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null, callable $onProgress = null);

    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     * @param callable|null $onProgress
     *
     * @return PromiseInterface
     */
    public function done(callable $onFulfilled = null, callable $onRejected = null, callable $onProgress = null);

    /**
     * @param callable $onRejected
     *
     * @return PromiseInterface
     */
    public function otherwise(callable $onRejected);

    /**
     * @param callable $onFulfilledOrRejected
     *
     * @return PromiseInterface
     */
    public function always(callable $onFulfilledOrRejected);

    /**
     * @param callable $onProgress
     *
     * @return PromiseInterface
     */
    public function progress(callable $onProgress);
}
