<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Behavior\Async;

use Tidal\WampWatch\Async\DeferredInterface;
use Tidal\WampWatch\Async\Adapter\DeferredFactoryInterface;
use Tidal\WampWatch\Async\DefaultDeferredFactory;
use Tidal\WampWatch\Async\Adapter\PromiseFactoryInterface;

/**
 * Trait Tidal\WampWatch\Behavior\Async\MakesDeferredPromisesTrait.
 */
trait MakesDeferredPromisesTrait
{
    /**
     * @var DeferredFactoryInterface
     */
    private $deferredFactory;

    /**
     * @param callable|null $canceller
     *
     * @return DeferredInterface
     */
    protected function createDeferred(callable $canceller = null)
    {
        return $this->getDeferredFactory()->create($canceller);
    }

    /**
     * @param DeferredFactoryInterface $deferredFactory
     */
    public function setDeferredFactory($deferredFactory)
    {
        $this->deferredFactory = $deferredFactory;
    }

    /**
     * @return DeferredFactoryInterface
     */
    public function getDeferredFactory()
    {
        if (!isset($this->deferredFactory)) {
            $this->deferredFactory = new DefaultDeferredFactory(
                $this->getPromiseFactory()
            );
        }

        return $this->deferredFactory;
    }

    /**
     * Dependency on MakesPromisesTrait.
     *
     * @return PromiseFactoryInterface
     */
    abstract public function getPromiseFactory();
}
