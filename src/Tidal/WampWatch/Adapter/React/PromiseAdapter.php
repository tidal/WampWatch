<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Adapter\React;

use Tidal\WampWatch\Async\PromiseInterface;
use React\Promise\Promise;

class PromiseAdapter implements PromiseInterface
{
    /**
     * @var Promise
     */
    private $adaptee;

    /**
     * PromiseAdapter constructor.
     *
     * @param \React\Promise\Promise $adaptee
     */
    public function __construct(Promise $adaptee)
    {
        $this->setAdaptee($adaptee);
    }

    /**
     * @param \React\Promise\Promise $adaptee
     */
    private function setAdaptee(Promise $adaptee)
    {
        $this->adaptee = $adaptee;
    }

    /**
     * @return \React\Promise\Promise
     */
    public function getAdaptee()
    {
        return $this->adaptee;
    }

    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     * @param callable|null $onProgress
     *
     * @return self
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null, callable $onProgress = null)
    {
        $this->adaptee->then($onFulfilled, $onRejected, $onProgress);

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
        $this->adaptee->done($onFulfilled, $onRejected, $onProgress);

        return $this;
    }

    /**
     * @param callable $onRejected
     *
     * @return self
     */
    public function otherwise(callable $onRejected)
    {
        $this->adaptee->otherwise($onRejected);

        return $this;
    }

    /**
     * @param callable $onFulfilledOrRejected
     *
     * @return self
     */
    public function always(callable $onFulfilledOrRejected)
    {
        $this->adaptee->always($onFulfilledOrRejected);

        return $this;
    }

    /**
     * @param callable $onProgress
     *
     * @return self
     */
    public function progress(callable $onProgress)
    {
        $this->adaptee->progress($onProgress);

        return $this;
    }
}
