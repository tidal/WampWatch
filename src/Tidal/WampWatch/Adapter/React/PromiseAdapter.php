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
use Tidal\WampWatch\Adapter\AdapterInterface;
use React\Promise\ExtendedPromiseInterface as ReactPromise;

/**
 * Class Tidal\WampWatch\Adapter\React\PromiseAdapter.
 */
class PromiseAdapter implements PromiseInterface, AdapterInterface
{
    /**
     * @var ReactPromise
     */
    private $adaptee;

    /**
     * PromiseAdapter constructor.
     *
     * @param ReactPromise $adaptee
     */
    public function __construct(ReactPromise $adaptee)
    {
        $this->setAdaptee($adaptee);
    }

    /**
     * @param ReactPromise $adaptee
     */
    private function setAdaptee(ReactPromise $adaptee)
    {
        $this->adaptee = $adaptee;
    }

    /**
     * @return ReactPromise
     */
    public function getAdaptee(): ReactPromise
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
    public function then(?callable $onFulfilled = null, ?callable $onRejected = null, ?callable $onProgress = null): self
    {
        $this->adaptee->then(function () use ($onFulfilled): mixed {
            if ($onFulfilled !== null) {
                return call_user_func_array($onFulfilled, func_get_args());
            }
        }, function () use ($onRejected) {
            if ($onRejected !== null) {
                return call_user_func_array($onRejected, func_get_args());
            }
        }, function () use ($onProgress) {
            if ($onProgress !== null) {
                return call_user_func_array($onProgress, func_get_args());
            }
        });

        return $this;
    }

    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     * @param callable|null $onProgress
     *
     * @return self
     */
    public function done(?callable $onFulfilled = null, ?callable $onRejected = null, ?callable $onProgress = null): self
    {
        $this->adaptee->done(function () use ($onFulfilled) {
            if ($onFulfilled !== null) {
                return call_user_func_array($onFulfilled, func_get_args());
            }
        }, function () use ($onRejected) {
            if ($onRejected !== null) {
                return call_user_func_array($onRejected, func_get_args());
            }
        }, function () use ($onProgress) {
            if ($onProgress !== null) {
                return call_user_func_array($onProgress, func_get_args());
            }
        });

        return $this;
    }

    /**
     * @param callable $onRejected
     *
     * @return self
     */
    public function otherwise(callable $onRejected): self
    {
        $this->adaptee->otherwise(function () use ($onRejected) {
            return call_user_func_array($onRejected, func_get_args());
        });

        return $this;
    }

    /**
     * @param callable $onAlways
     *
     * @return self
     */
    public function always(callable $onAlways): self
    {
        $this->adaptee->always(function () use ($onAlways) {
            return call_user_func_array($onAlways, func_get_args());
        });

        return $this;
    }

    /**
     * @param callable $onProgress
     *
     * @return self
     */
    public function progress(callable $onProgress): self
    {
        $this->adaptee->progress(function () use ($onProgress) {
            return call_user_func_array($onProgress, func_get_args());
        });

        return $this;
    }
}
