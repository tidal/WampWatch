<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Subscription;

use Thruway\Message\SubscribedMessage;
use Tidal\WampWatch\Async\PromiseInterface;
use Tidal\WampWatch\ClientSessionInterface as ClientSession;
use Tidal\WampWatch\Util;
use Tidal\WampWatch\Behavior\Async\MakesPromisesTrait;
use Tidal\WampWatch\Behavior\Async\MakesDeferredPromisesTrait;
use Tidal\WampWatch\Async\DeferredInterface;

/**
 * Class Tidal\WampWatch\Subscription\Collection.
 */
class Collection
{
    use MakesPromisesTrait,
        MakesDeferredPromisesTrait;

    /**
     * The collection's WAMP client session.
     *
     * @var ClientSession
     */
    private $session;

    /**
     * @var array list of subscriptions with topic as key and subscription-id as value
     */
    private $subscriptions = [];

    /**
     * @var array list of subscriptions callbacks with topic as key
     */
    private $subscriptionCallbacks = [];

    /**
     * @var bool if the collection is successfully subscribed to all topics
     */
    private $isSubscribed = false;

    /**
     * @var bool if the collection is currently trying to subscribe to all topics
     */
    private $isSubscribing = false;

    /**
     * @var DeferredInterface
     */
    private $subscriptionPromise;

    /**
     * Collection constructor.
     *
     * @param \Tidal\WampWatch\ClientSessionInterface $session
     */
    public function __construct(ClientSession $session)
    {
        $this->session = $session;
    }

    /**
     * @param string   $topic    the topic the subscription is for
     * @param callable $callback the callback for the topic
     */
    public function addSubscription($topic, callable $callback): void
    {
        $this->subscriptions[$topic] = 0;
        $this->subscriptionCallbacks[$topic] = $callback;
    }

    /**
     * @return bool
     */
    public function hasSubscription(): bool
    {
        return count($this->subscriptions) > 0;
    }

    /**
     * Subscribe to all topics added with 'addSubscription'.
     * Returns false if already subscribed or curretly subscribing.
     *
     * @return PromiseInterface
     */
    public function subscribe(): PromiseInterface
    {
        if (!$this->isSubscribed() && !$this->isSubscribing()) {
            $this->subscriptionPromise = $this->createDeferred();
            $this->isSubscribing = true;
            $this->doSubscribe();
        }

        return $this->subscriptionPromise->promise();
    }

    protected function doSubscribe(): void
    {
        $this->getPromiseFactory()->all($this->getSubscriptionPromises())->done(function () {
            $this->isSubscribed = true;
            $this->isSubscribing = false;
            $this->subscriptionPromise->resolve($this->subscriptions);
        });
    }

    /**
     * @return PromiseInterface[]
     */
    private function getSubscriptionPromises(): array
    {
        $promises = [];

        foreach (array_keys($this->subscriptions) as $topic) {
            $promises[] = $this->session->subscribe($topic, $this->subscriptionCallbacks[$topic])
                ->then(function (SubscribedMessage $msg) use ($topic) {
                    $this->subscriptions[$topic] = $msg->getSubscriptionId();
                    $this->subscriptionPromise->notify($topic);

                    return $topic;
                });
        }

        return $promises;
    }

    /**
     * @return PromiseInterface
     */
    public function unsubscribe(): PromiseInterface
    {
        $resolver = function (callable $resolve) {
            $resolve();
        };
        $promise = $this->createPromise($resolver);

        if ($this->isSubscribed()) {
            foreach ($this->subscriptions as $subId) {
                Util::unsubscribe($this->session, $subId);
            }

            $this->isSubscribed = false;
        }

        return $promise;
    }

    /**
     * @return bool
     */
    public function isSubscribed(): bool
    {
        return $this->isSubscribed;
    }

    /**
     * @return bool
     */
    public function isSubscribing(): bool
    {
        return $this->isSubscribing;
    }
}
