<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */


namespace Tidal\WampWatch\Adapter\Thruway;

use Thruway\ClientSession as ThruwaySession;
use Tidal\WampWatch\ClientSessionInterface;

class ClientSession implements ClientSessionInterface
{
    /**
     * @var ThruwaySession
     */
    protected $thruwaySession;

    public function __construct(ThruwaySession $thruwaySession)
    {
        $this->thruwaySession = $thruwaySession;
    }

    /**
     * Subscribe.
     *
     * @param string   $topicName
     * @param callable $callback
     * @param          $options   array
     *
     * @return \React\Promise\Promise
     */
    public function subscribe($topicName, callable $callback, $options = null)
    {
        return $this->thruwaySession->subscribe($topicName, $callback, $options);
    }

    /**
     * Publish.
     *
     * @param string      $topicName
     * @param array|mixed $arguments
     * @param array|mixed $argumentsKw
     * @param array|mixed $options
     *
     * @return \React\Promise\Promise
     */
    public function publish($topicName, $arguments = null, $argumentsKw = null, $options = null)
    {
        return $this->thruwaySession->publish($topicName, $arguments, $argumentsKw, $options);
    }

    /**
     * Register.
     *
     * @param string      $procedureName
     * @param callable    $callback
     * @param array|mixed $options
     *
     * @return \React\Promise\Promise
     */
    public function register($procedureName, callable $callback, $options = null)
    {
        return $this->thruwaySession->register($procedureName, $callback, $options);
    }

    /**
     * Unregister.
     *
     * @param string $procedureName
     *
     * @return \React\Promise\Promise
     */
    public function unregister($procedureName)
    {
        return $this->thruwaySession->unregister($procedureName);
    }

    /**
     * Call.
     *
     * @param string      $procedureName
     * @param array|mixed $arguments
     * @param array|mixed $argumentsKw
     * @param array|mixed $options
     *
     * @return \React\Promise\Promise
     */
    public function call($procedureName, $arguments = null, $argumentsKw = null, $options = null)
    {
        return $this->thruwaySession->call($procedureName, $arguments, $argumentsKw, $options);
    }

    /**
     * @param int $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->thruwaySession->setSessionId($sessionId);
    }

    /**
     * @return int the Session Id
     */
    public function getSessionId()
    {
        return $this->thruwaySession->getSessionId();
    }

    public function sendMessage($msg)
    {
        $this->thruwaySession->sendMessage($msg);
    }
}
