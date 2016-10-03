<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Adapter\React;

use Tidal\WampWatch\Async\Adapter\PromiseFactoryInterface;
use React\Promise\Promise as ReactPromise;

class PromiseFactory implements PromiseFactoryInterface
{
    /**
     * @param callable      $resolver
     * @param callable|null $canceller
     *
     * @return PromiseAdapter
     */
    public function create(callable $resolver, callable $canceller = null)
    {
        return $this->createFromAdaptee(
            new ReactPromise(
                $resolver,
                $canceller
            )
        );
    }

    /**
     * @param ReactPromise $adaptee
     *
     * @return PromiseAdapter
     */
    public function createFromAdaptee($adaptee)
    {
        return new PromiseAdapter($adaptee);
    }
}
