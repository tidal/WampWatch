<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch;

use Evenement\EventEmitterTrait;
use React\Promise\Promise;
use Tidal\WampWatch\ClientSessionInterface as ClientSession;
use Tidal\WampWatch\Subscription\Collection as SubscriptionCollection;

/**
 * Description of MonitorTrait.
 *
 * @author Timo
 */
trait MonitorTrait
{
    use EventEmitterTrait;

    /**
     * The monitor's WAMP client session.
     *
     * @var ClientSession
     */
    protected $session;

    /**
     * if the monitor is running.
     *
     * @var bool
     */
    protected $isRunning = false;

    /**
     * @var SubscriptionCollection collection for meta subscriptions
     */
    protected $metaSubscriptionCollection;

    /**
     * @var string
     */
    protected $initialCallProcedure;

    /**
     * @var callable
     */
    protected $initialCallCallback;

    /**
     * @var bool
     */
    protected $initialCallDone = false;

    /**
     * @param ClientSession $session
     */
    protected function setClientSession(ClientSession $session)
    {
        $this->session = $session;
    }

    /**
     * Start the monitor.
     *
     * @return bool
     */
    public function start()
    {
        $this->getMetaSubscriptionCollection()->subscribe()->done(function () {
            $this->checkStarted();
        });
        $this->callInitialProcedure()->done(function () {
            $this->checkStarted();
        });

        return true;
    }

    /**
     * Stop the monitor.
     * Returns boolean if the monitor could be started.
     *
     * @return bool
     */
    public function stop()
    {
        if (!$this->isRunning()) {
            return false;
        }

        $this->getMetaSubscriptionCollection()->unsubscribe();

        $this->isRunning = false;
        $this->emit('stop', [$this]);

        return true;
    }

    protected function getList()
    {
        return [];
    }

    /**
     * Get the monitor's WAMP client session.
     *
     * @return ClientSession
     */
    public function getServerSession()
    {
        return $this->session;
    }

    /**
     * Get the monitor's running state.
     *
     * @return bool
     */
    public function isRunning()
    {
        return $this->isRunning;
    }

    /**
     * @param \Tidal\WampWatch\Subscription\Collection $collection
     */
    public function setMetaSubscriptionCollection(SubscriptionCollection $collection)
    {
        $this->metaSubscriptionCollection = $collection;
    }

    /**
     * @return \Tidal\WampWatch\Subscription\Collection
     */
    public function getMetaSubscriptionCollection()
    {
        return isset($this->metaSubscriptionCollection)
            ? $this->metaSubscriptionCollection
            : $this->metaSubscriptionCollection = new SubscriptionCollection($this->session);
    }

    protected function setInitialCall($procedure, callable $callback)
    {
        $this->initialCallProcedure = (string)$procedure;
        $this->initialCallCallback = $callback;
    }

    /**
     * @return \React\Promise\Promise
     */
    protected function callInitialProcedure()
    {
        if (!isset($this->initialCallProcedure) || !isset($this->initialCallCallback)) {
            $this->initialCallDone = true;
            $resolver = function (callable $resolve) {
                $resolve();
            };

            return new  Promise($resolver);
        }

        return $this->session->call($this->initialCallProcedure, [])->then(function ($res) {
            $this->initialCallDone = true;
            $cb = $this->initialCallCallback;
            $cb($res);

            return $res;
        });
    }

    /**
     * Checks if all necessary subscriptions and calls have been responded to.
     */
    protected function checkStarted()
    {
        if ($this->isMetaSubscribed() &&
            $this->initialCallDone &&
            !$this->isRunning()
        ) {
            $this->isRunning = true;
            $this->emit('start', [$this->getList()]);
        }
    }

    protected function isMetaSubscribed()
    {
        if (!$this->getMetaSubscriptionCollection()->hasSubscription()) {
            return true;
        }

        return $this->getMetaSubscriptionCollection()->isSubscribed();
    }
}
