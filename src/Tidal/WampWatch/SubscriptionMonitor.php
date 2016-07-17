<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Phaim\Server\Wamp\Monitor;

use Phaim\Server\Wamp\Util;

/**
 * Description of SessionMonitor.
 *
 * @author Timo
 */
class SubscriptionMonitor implements MonitorInterface
{
    use MonitorTrait {
        start as doStart;
        stop as doStop;
    }

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
    protected $subscriptionIds = [];

    public function start()
    {
        $this->once('list', function () {
            $this->startSubscriptions();
            $this->doStart();
        });
        $this->retrieveSubscriptionIds();
    }

    public function stop()
    {
        $this->stopSubscriptions();
        $this->doStart();
    }

    public function getSessionInfo($sessionId, callable $callback)
    {
        return $this->session->call(self::SUBSCRIPTION_INFO_TOPIC, $sessionId)->then(
            function ($res) use ($callback) {
                $this->emit('info', $res);
                $callback($res[0]);
            },
            function ($error) {
                $this->emit('error', [$error]);
            }
        );
    }

    public function getSessionIds(callable $callback)
    {
        if (!count($this->subscriptionIds)) {
            $this->retrieveSubscriptionIds($callback);

            return;
        }

        $callback($this->subscriptionIds);
    }

    protected function startSubscriptions()
    {
        $this->session->subscribe(self::SUBSCRIPTION_CREATE_TOPIC, function ($res) {
            return $this->createHandler($res);
        });
        $this->session->subscribe(self::SUBSCRIPTION_DELETE_TOPIC, function ($res) {
            return $this->deleteHandler($res);
        });
        $this->session->subscribe(self::SUBSCRIPTION_SUB_TOPIC, function ($res) {
            return $this->subHandler($res);
        });
    }

    protected function createHandler($res)
    {
        $sessionInfo = $res[0];
        $sessionId = $sessionInfo['session'];
        if ((array_search($sessionId, $this->subscriptionIds)) === false) {
            $this->subscriptionIds[] = $sessionId;
            $this->emit('create', [$sessionInfo]);
        }
    }

    protected function deleteHandler($res)
    {
        $sessionId = $res[0];
        if (($key = array_search($sessionId, $this->subscriptionIds)) !== false) {
            unset($this->subscriptionIds[$key]);
            $this->emit('delete', [$sessionId]);
        }
    }

    protected function subHandler($res)
    {
        $sessionId = $res[0];
        $subId = $res[1];
        $this->getSubscriptionDetail($subId)->then(
            function ($res) use ($sessionId, $subId) {
                $this->emit('sub', [$sessionId, $subId, $res[0]]);
            },
            function () {
                $this->emit('sub', [$sessionId, $subId, [
                    'id' => $subId,
                    'created' => null,
                    'uri' => null,
                    'match' => null,
                ]]);
            }
        );
    }

    protected function stopSubscriptions()
    {
        Util::unsubscribe($this->session, self::SUBSCRIPTION_JOIN_TOPIC);
        Util::unsubscribe($this->session, self::SUBSCRIPTION_LEAVE_TOPIC);
    }

    public function getSubscriptionDetail($subId, callable $callback)
    {
        return $this->session->call(self::SUBSCRIPTION_GET_TOPIC, [$subId])->then(
            function ($res) use ($callback) {
                $this->emit('info', [$res[0]]);
                $callback($res[0]);
            },
            function ($error) {
                $this->emit('error', [$error]);
            }
        );
    }

    protected function retrieveSubscriptionIds(callable $callback = null)
    {
        return $this->session->call(self::SUBSCRIPTION_LIST_TOPIC, [])->then(
            function ($res) use ($callback) {
                $this->subscriptionIds = $res[0];
                $this->emit('list', [$this->subscriptionIds]);
                if ($callback) {
                    $callback($this->subscriptionIds);
                }
            },
            function ($error) {
                $this->emit('error', [$error]);
            }
        );
    }

    protected function getList()
    {
        return $this->subscriptionIds;
    }
}
