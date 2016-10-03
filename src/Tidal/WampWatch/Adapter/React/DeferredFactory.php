<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Adapter\React;

use Tidal\WampWatch\Async\Adapter\PromiseFactoryInterface;
use React\Promise\Deferred as ReactDeferred;

class DeferredFactory
{
    /**
     * @var PromiseFactoryInterface
     */
    private $promiseFactory;

    public function __construct(PromiseFactoryInterface $promiseFactory)
    {
        $this->setPromiseFactory($promiseFactory);
    }

    /**
     * @param PromiseFactoryInterface $promiseFactory
     */
    private function setPromiseFactory(PromiseFactoryInterface $promiseFactory)
    {
        $this->promiseFactory = $promiseFactory;
    }

    /**
     * @return PromiseFactoryInterface
     */
    public function getPromiseFactory()
    {
        return $this->promiseFactory;
    }

    /**
     * @param callable|null $canceller
     *
     * @return DeferredAdapter
     */
    public function create(callable $canceller = null)
    {
        $adapter = new DeferredAdapter(
            new ReactDeferred($canceller)
        );
        $adapter->setPromiseFactory($this->getPromiseFactory());

        return $adapter;
    }
}