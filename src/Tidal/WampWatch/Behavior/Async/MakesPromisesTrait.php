<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Behavior\Async;

use Tidal\WampWatch\Async\PromiseInterface;
use Tidal\WampWatch\Async\Adapter\PromiseFactoryInterface;
use RuntimeException;

trait MakesPromisesTrait
{
    /**
     * @var PromiseFactoryInterface
     */
    private $promiseFactory;

    /**
     * @param callable      $resolver
     * @param callable|null $canceller
     *
     * @return PromiseInterface
     */
    protected function createPromise(callable $resolver, callable $canceller = null)
    {
        return $this->getPromiseFactory()->create($resolver, $canceller);
    }

    /**
     * @param PromiseFactoryInterface $promiseFactory
     */
    public function setPromiseFactory($promiseFactory)
    {
        $this->promiseFactory = $promiseFactory;
    }

    /**
     * @return PromiseFactoryInterface
     */
    public function getPromiseFactory()
    {
        if (!isset($this->promiseFactory)) {
            throw new RuntimeException('Promise Factory not set.');
        }

        return $this->promiseFactory;
    }
}
