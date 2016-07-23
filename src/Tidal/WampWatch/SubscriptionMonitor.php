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

use React\Promise\Promise;
use Tidal\WampWatch\ClientSessionInterface as ClientSession;

/**
 * Description of SessionMonitor.
 *
 * @author Timo
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

    const LOOKUP_MATCH_WILDCARD = 'wildcard';
    const LOOKUP_MATCH_PREFIX = 'prefix';

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
     * @return Promise
     */
    public function getSubscriptionInfo($topic)
    {
        return $this->session->call(self::SUBSCRIPTION_GET_TOPIC, $topic)->then(
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

        return new Promise(function (callable $resolve) {
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

    private function getCreateHandler()
    {
        return function ($res) {
            $sessionId = $res[0];
            $subscriptionInfo = $res[1];
            $this->emit('create', [$sessionId, $subscriptionInfo]);
        };
    }

    private function getSubscriptionHandler($event)
    {
        return function ($res) use ($event) {
            $sessionId = $res[0];
            $subscriptionId = $res[1];
            $this->emit($event, [$sessionId, $subscriptionId]);
        };
    }

    protected function retrieveSubscriptionIds()
    {
        return $this->session->call(self::SUBSCRIPTION_LIST_TOPIC, [])
            ->then(
                $this->getSubscriptionIdRetrievalCallback(),
                $this->getErrorCallback()
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

    protected function getSubscriptionIdRetrievalCallback()
    {
        return function ($res) {
            $this->setList($res);
            $this->emit('list', [
                $this->subscriptionIds->exact,
                $this->subscriptionIds->prefix,
                $this->subscriptionIds->wildcard,
            ]);

            return $res;
        };
    }
}
