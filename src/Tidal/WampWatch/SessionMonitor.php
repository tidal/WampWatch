<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
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
        stop as doStop;
    }

    const SESSION_JOIN_TOPIC = 'wamp.session.on_join';
    const SESSION_LEAVE_TOPIC = 'wamp.session.on_leave';
    const SESSION_COUNT_TOPIC = 'wamp.session.count';
    const SESSION_LIST_TOPIC = 'wamp.session.list';
    const SESSION_INFO_TOPIC = 'wamp.session.get';

    protected $sessionIds = [];

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
        $this->once('list', function () {
            $this->doStart();
        });
        $this->startSubscriptions();
        $this->retrieveSessionIds();

        return true;
    }

    /**
     * Stop the monitor.
     * Returns boolean wether the monitor could be started.
     *
     * @return bool
     */
    public function stop()
    {
        $this->stopSubscriptions();
        $this->doStop();

        return true;
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
    public function getSessionInfo($sessionId, callable $callback)
    {
        return $this->session->call(self::SESSION_INFO_TOPIC, [$sessionId])->then(
            function ($res) use ($callback) {
                $this->emit('info', [$res[0]]);
                $callback($res[0]);
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
    protected function startSubscriptions()
    {
        // subscription to 'wamp.session.on_join'
        $this->session->subscribe(self::SESSION_JOIN_TOPIC, function (array $res) {
            $sessionInfo = $res[0];
            if (!$this->validateSessionInfo($sessionInfo) || $this->hasSession($sessionInfo)) {
                return;
            }

            $this->addSession($sessionInfo);
        });
        // subscription to 'wamp.session.on_leave'
        $this->session->subscribe(self::SESSION_LEAVE_TOPIC, function (array $res) {
            // @bug : wamp.session.on_leave is bugged as of crossbar.io 0.11.0
            // will provide sessionID when Browser closes/reloads,
            // but not when calling connection.close();
            $sessionId = (int)$res[0];
            $this->removeSessionId($sessionId);
        });
    }

    /**
     * Unsubscribes from the meta-events.
     */
    protected function stopSubscriptions()
    {
        Util::unsubscribe($this->session, self::SESSION_JOIN_TOPIC);
        Util::unsubscribe($this->session, self::SESSION_LEAVE_TOPIC);
    }

    /**
     * Retrieves the list of current sessionIds on the router.
     *
     * @param callable|null $callback
     */
    protected function retrieveSessionIds(callable $callback = null)
    {
        $this->session->call(self::SESSION_LIST_TOPIC, [])->then(
            function ($res) use ($callback) {
                // remove our own sessionID from the tracked sessions
                $sessionIds = $this->removeOwnSessionId($res[0]);
                $this->setList($sessionIds);
                $this->emit('list', [$this->getList()]);
                if ($callback !== null) {
                    $callback($this->sessionIds);
                }
            },
            function ($error) {
                $this->emit('error', [$error]);
            }
        );
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
        }

        return $sessionsIds;
    }
}
