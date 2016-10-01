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

use Tidal\WampWatch\Async\DeferredInterface;
use Tidal\WampWatch\Async\PromiseInterface;
use React\Promise\Deferred;

class DeferredAdapter implements DeferredInterface
{
    /**
     * @var Deferred
     */
    private $adaptee;

    /**
     * @var string fully qualified class name of the promise to create
     */
    private $promiseClass = PromiseAdapter::class;

    /**
     * DeferredAdapter constructor.
     *
     * @param \React\Promise\Deferred $adaptee
     */
    public function __construct(Deferred $adaptee)
    {
        $this->setAdaptee($adaptee);
    }

    /**
     * @param \React\Promise\Deferred $adaptee
     */
    private function setAdaptee(Deferred $adaptee)
    {
        $this->adaptee = $adaptee;
    }

    /**
     * @return mixed
     */
    public function getAdaptee()
    {
        return $this->adaptee;
    }

    /**
     * @param string $promiseClass
     */
    public function setPromiseClass($promiseClass)
    {
        $this->promiseClass = $promiseClass;
    }

    /**
     * @return string
     */
    public function getPromiseClass()
    {
        return $this->promiseClass;
    }

    /**
     * @return PromiseInterface
     */
    public function promise()
    {
        return $this->createPromise();
    }

    /**
     * @param null $value
     */
    public function resolve($value = null)
    {
        $this->adaptee->resolve($value);
    }

    /**
     * @param null $reason
     */
    public function reject($reason = null)
    {
        $this->adaptee->reject($reason);
    }

    /**
     * @param null $update
     */
    public function notify($update = null)
    {
        $this->adaptee->notify($update);
    }

    /**
     * @return PromiseInterface
     */
    private function createPromise()
    {
        return new $this->promiseClass(
            $this->adaptee->promise()
        );
    }
}
