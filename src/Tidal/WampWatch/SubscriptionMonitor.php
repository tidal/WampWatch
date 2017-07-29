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

use Tidal\WampWatch\Async\PromiseInterface;
use Tidal\WampWatch\ClientSessionInterface as ClientSession;

/**
 * Class SubscriptionMonitor.
 */
class SubscriptionMonitor implements MonitorInterface
{
    use MonitorTrait;

    const SUBSCRIPTION_CREATE_TOPIC = 'wamp.subscription.on_create';
    const SUBSCRIPTION_SUB_TOPIC = 'wamp.subscription.on_subscribe';
    const SUBSCRIPTION_UNSUB_TOPIC = 'wamp.subscription.on_unsubscribe';
    const SUBSCRIPTION_DELETE_TOPIC = 'wamp.subscription.on_delete';
    const SUBSCRIPTION_LIST_TOPIC = 'wamp.subscription.list';
    const SUBSCRIPTION_LOOKUP_TOPIC = 'wamp.subscription.lookup';
    const SUBSCRIPTION_MATCH_TOPIC = 'wamp.subscription.match';
    const SUBSCRIPTION_GET_TOPIC = 'wamp.subscription.get';
    const SUBSCRIPTION_SUBLIST_TOPIC = 'wamp.subscription.list_subscribers';
    const SUBSCRIPTION_SUBCOUNT_TOPIC = 'wamp.subscription.count_subscribers';

    protected $sessionIds = [];

    /**
     * @var \stdClass Objects withs lists of subscriptions (exact, prefix, wildcard)
     */
    protected $subscriptionIds = null;

    /**
     * Constructor.
     *
     * @param ClientSession $session
     */
    public function __construct(ClientSession $session)
    {
        $this->setClientSession($session);
        $this->initSetupCalls();
    }

    /**
     * @param string $topic
     *
     * @return PromiseInterface
     */
    public function getSubscriptionInfo($topic)
    {
        return $this->session->call(self::SUBSCRIPTION_GET_TOPIC, [$topic])->then(
            function ($res) {
                $this->emit('info', [$res]);

                return $res;
            },
            $this->getErrorCallback()
        );
    }

    public function getSubscriptionIds()
    {
        if (!isset($this->subscriptionIds)) {
            return $this->retrieveSubscriptionIds();
        }

        return $this->createPromiseAdapter(function (callable $resolve) {
            $resolve($this->subscriptionIds);
        });
    }

    /**
     * Initializes the subscription to the meta-events.
     */
    protected function initSetupCalls()
    {
        // @var \Tidal\WampWatch\Subscription\Collection
        $collection = $this->getMetaSubscriptionCollection();

        $collection->addSubscription(self::SUBSCRIPTION_CREATE_TOPIC, $this->getCreateHandler());
        $collection->addSubscription(self::SUBSCRIPTION_DELETE_TOPIC, $this->getSubscriptionHandler('delete'));
        $collection->addSubscription(self::SUBSCRIPTION_SUB_TOPIC, $this->getSubscriptionHandler('subscribe'));
        $collection->addSubscription(self::SUBSCRIPTION_UNSUB_TOPIC, $this->getSubscriptionHandler('unsubscribe'));

        $this->setInitialCall(self::SUBSCRIPTION_LIST_TOPIC, $this->getSubscriptionIdRetrievalCallback());
    }

    protected function retrieveSubscriptionIds()
    {
        return $this->retrieveCallData(
            self::SUBSCRIPTION_LIST_TOPIC,
            $this->getSubscriptionIdRetrievalCallback(),
            []
        );
    }

    protected function setList($list)
    {
        $this->subscriptionIds = $list;
    }

    protected function getList()
    {
        return $this->subscriptionIds;
    }
}
