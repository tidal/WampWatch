<?php
/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Test\Unit\Stub;

use Tidal\WampWatch\Async\PromiseInterface;

class PromiseStub implements PromiseInterface
{
    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     * @param callable|null $onProgress
     *
     * @return self
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null, callable $onProgress = null)
    {
        return $this;
    }

    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     * @param callable|null $onProgress
     *
     * @return self
     */
    public function done(callable $onFulfilled = null, callable $onRejected = null, callable $onProgress = null)
    {
        return $this;
    }

    /**
     * @param callable $onRejected
     *
     * @return self
     */
    public function otherwise(callable $onRejected)
    {
        return $this;
    }

    /**
     * @param callable $onFulfilledOrRejected
     *
     * @return self
     */
    public function always(callable $onFulfilledOrRejected)
    {
        return $this;
    }

    /**
     * @param callable $onProgress
     *
     * @return self
     */
    public function progress(callable $onProgress)
    {
        return $this;
    }
}
