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

use Evenement\EventEmitterInterface;
use Tidal\WampWatch\ClientSessionInterface as ClientSession;

/**
 * Description of SessionMonitor.
 *
 * @author Timo
 */
class SessionMonitor implements MonitorInterface, EventEmitterInterface
{
    use MonitorTrait {
        start as doStart;
    }

    const SESSION_JOIN_TOPIC = 'wamp.session.on_join';
    const SESSION_LEAVE_TOPIC = 'wamp.session.on_leave';
    const SESSION_COUNT_TOPIC = 'wamp.session.count';
    const SESSION_LIST_TOPIC = 'wamp.session.list';
    const SESSION_INFO_TOPIC = 'wamp.session.get';

    /**
     * @var array monitored session ids
     */
    protected $sessionIds = [];

    /**
     * @var int subscription id for on_join
     */
    protected $joinSubscriptionId = 0;

    /**
     * @var int subscription id for on_leave
     */
    protected $leaveSubscriptionId = 0;

    /**
     * @var bool flag if list call has returned
     */
    protected $calledList = false;

    /**
     * Constructor.
     *
     * @param ClientSession $session
     */
    public function __construct(ClientSession $session)
    {
        $this->setClientSession($session);
    }

    /**
     * Start the monitor.
     *
     * @return bool
     */
    public function start()
    {
        $this->initSetupCalls();
        $this->getSubscriptionCollection()->subscribe()->done(function () {
            $this->checkStarted();
        });
        $this->retrieveSessionIds();

        return true;
    }

    /**
     * Checks if all necessary subscriptions and calls have been responded to.
     */
    protected function checkStarted()
    {
        if ($this->getSubscriptionCollection()->isSubscribed() &&
            $this->calledList &&
            !$this->isRunning()
        ) {
            $this->doStart();
        }
    }

    /**
     * Retrieves the session-info for given sessionId
     * and populates it in via given callback.
     *
     * @param          $sessionId
     * @param callable $callback
     *
     * @return mixed
     */
    public function getSessionInfo($sessionId, callable $callback = null)
    {
        return $this->session->call(self::SESSION_INFO_TOPIC, [$sessionId])->then(
            function ($res) use ($callback) {
                $this->emit('info', [$res[0]]);
                if ($callback !== null) {
                    $callback($res[0]);
                }
            },
            function ($error) {
                $this->emit('error', [$error]);
            }
        );
    }

    /**
     * Retrieves the Ids of the sessions currently
     * registered on the wamp-router in the monitor's realm
     * and populates the data via given callback,.
     *
     * @param callable $callback
     *
     * @return mixed
     */
    public function getSessionIds(callable $callback)
    {
        if (!count($this->sessionIds)) {
            $this->retrieveSessionIds($callback);

            return;
        }

        $callback($this->sessionIds);
    }

    /**
     * Checks if a session id is known.
     *
     * @param $sessionId
     *
     * @return bool
     */
    public function hasSessionId($sessionId)
    {
        return array_search($sessionId, $this->sessionIds) !== false;
    }

    /**
     * Removes a session id.
     *
     * @param int $sessionId
     */
    protected function removeSessionId($sessionId)
    {
        if (!$this->hasSessionId($sessionId)) {
            return;
        }
        $key = array_search($sessionId, $this->sessionIds);
        unset($this->sessionIds[$key]);
        $this->sessionIds = array_values($this->sessionIds);
        $this->emit('leave', [$sessionId]);
    }

    /**
     * Checks if a session is known by extracting its id.
     *
     * @param $sessionInfo
     *
     * @return bool
     */
    protected function hasSession($sessionInfo)
    {
        return $this->hasSessionId($sessionInfo->session);
    }

    /**
     * Adds and publishes a joined session.
     *
     * @param $sessionInfo
     */
    protected function addSession($sessionInfo)
    {
        $this->sessionIds[] = $sessionInfo->session;
        $this->emit('join', [$sessionInfo]);
    }

    /**
     * Validates the sessionInfo sent from the router.
     *
     * @param string $sessionInfo
     *
     * @return bool
     */
    protected function validateSessionInfo($sessionInfo)
    {
        return is_object($sessionInfo) && property_exists($sessionInfo, 'session');
    }

    /**
     * Initializes the subscription to the meta-events.
     */
    protected function initSetupCalls()
    {
        // @var \Tidal\WampWatch\Subscription\Collection
        $collection = $this->getSubscriptionCollection();

        $collection->addSubscription(self::SESSION_JOIN_TOPIC, function (array $res) {
            $sessionInfo = $res[0];
            if (!$this->validateSessionInfo($sessionInfo) || $this->hasSession($sessionInfo)) {
                return;
            }
            $this->addSession($sessionInfo);
        });

        $collection->addSubscription(self::SESSION_LEAVE_TOPIC, function (array $res) {
            // @bug : wamp.session.on_leave is bugged as of crossbar.io 0.11.0
            // will provide sessionID when Browser closes/reloads,
            // but not when calling connection.close();
            $sessionId = (int) $res[0];
            $this->removeSessionId($sessionId);
        });

        $this->setInitialCall(self::SESSION_LIST_TOPIC, $this->getSessionIdRetrievalCallback());
    }

    /**
     * Retrieves the list of current sessionIds on the router.
     *
     * @param callable|null $callback
     */
    protected function retrieveSessionIds(callable $callback = null)
    {
        $this->session->call(self::SESSION_LIST_TOPIC, [])
            ->then(
                $this->getSessionIdRetrievalCallback()
            )->done(function ($res) use ($callback) {
                if ($callback !== null) {
                    $callback($res);
                }
            });
    }

    protected function getSessionIdRetrievalCallback()
    {
        return function ($res) {
            // remove our own sessionID from the tracked sessions
            $sessionIds = $this->removeOwnSessionId($res[0]);
            $this->setList($sessionIds);
            $this->emit('list', [$this->getList()]);
            $this->calledList = true;
            $this->checkStarted();

            return $this->getList();
        };
    }

    protected function setList($list)
    {
        $this->sessionIds = $list;
    }

    protected function getList()
    {
        return $this->sessionIds;
    }

    /**
     * remove the sessionID of the Monitor from the list.
     *
     * @param array $sessionsIds
     *
     * @return mixed
     */
    protected function removeOwnSessionId(array $sessionsIds)
    {
        $key = array_search($this->session->getSessionId(), $sessionsIds);
        if ($key >= 0) {
            unset($sessionsIds[$key]);
            $sessionsIds = array_values($sessionsIds);
        }

        return $sessionsIds;
    }
}
