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

use Tidal\WampWatch\Async\Adapter\PromiseFactoryInterface;
use React\Promise as ReactCombinators;
use React\Promise\Promise as ReactPromise;
use Tidal\WampWatch\Async\PromiseInterface;
use InvalidArgumentException;

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

    /**
     * @param PromiseInterface[]|ReactPromise[] $promises
     *
     * @return PromiseAdapter
     */
    public function all(array $promises)
    {
        return $this->createFromAdaptee(
            ReactCombinators\all(
                self::extractReactPromises(
                    $promises
                )
            )
        );
    }

    /**
     * @param PromiseInterface[]|ReactPromise[] $promises
     *
     * @return PromiseAdapter
     */
    public function any(array $promises)
    {
        return $this->createFromAdaptee(
            ReactCombinators\all(
                self::extractReactPromises(
                    $promises
                )
            )
        );
    }

    /**
     * @param PromiseInterface[]|ReactPromise[] $promises
     * @param int $count
     *
     * @return PromiseAdapter
     */
    public function some(array $promises, int $count)
    {
        return $this->createFromAdaptee(
            ReactCombinators\some(
                self::extractReactPromises(
                    $promises
                ),
                $count
            )
        );
    }

    /**
     * @param PromiseInterface[]|ReactPromise[] $promises
     *
     * @return PromiseAdapter
     */
    public function first(array $promises)
    {
        return $this->createFromAdaptee(
            ReactCombinators\race(
                self::extractReactPromises(
                    $promises
                )
            )
        );
    }

    /**
     * @param PromiseInterface[]|ReactPromise[] $promises
     *
     * @return ReactPromise[]
     *
     * @throws InvalidArgumentException
     */
    protected static function extractReactPromises(array $promises)
    {
        $results = [];
        foreach ($promises as $promise) {
            if (!$promise instanceof PromiseInterface && !$promise instanceof ReactPromise) {
                throw new InvalidArgumentException(
                    sprintf(
                        "Promises must be either instance of '%s' or '%s' in %s",
                        PromiseInterface::class,
                        ReactPromise::class,
                        __METHOD__
                    )
                );
            }

            $results[] = $promise instanceof PromiseAdapter
                ? $promise->getAdaptee()
                : $promise;
        }

        return $results;
    }
}
