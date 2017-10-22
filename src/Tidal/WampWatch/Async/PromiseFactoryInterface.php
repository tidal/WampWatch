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

interface PromiseFactoryInterface
{
    /**
     * @param callable      $resolver
     * @param callable|null $canceller
     *
     * @return PromiseInterface
     */
    public function create(callable $resolver, callable $canceller = null);

    /**
     * @param PromiseInterface[] $promises
     *
     * @return PromiseInterface
     */
    public function all(array $promises);

    /**
     * @param PromiseInterface[] $promises
     *
     * @return PromiseInterface
     */
    public function any(array $promises);

    /**
     * @param PromiseInterface[] $promises
     * @param int                $count
     *
     * @return PromiseInterface
     */
    public function some(array $promises, int $count);

    /**
     * @param PromiseInterface[] $promises
     *
     * @return PromiseInterface
     */
    public function first(array $promises);
}
