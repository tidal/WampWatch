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

interface ClientSessionInterface
{
    /**
     * Subscribe.
     *
     * @param string   $topicName
     * @param callable $callback
     * @param $options array
     *
     * @return PromiseInterface
     */
    public function subscribe($topicName, callable $callback, $options = null);

    /**
     * Publish.
     *
     * @param string      $topicName
     * @param array|mixed $arguments
     * @param array|mixed $argumentsKw
     * @param array|mixed $options
     *
     * @return PromiseInterface
     */
    public function publish($topicName, $arguments = null, $argumentsKw = null, $options = null);

    /**
     * Register.
     *
     * @param string      $procedureName
     * @param callable    $callback
     * @param array|mixed $options
     *
     * @return PromiseInterface
     */
    public function register($procedureName, callable $callback, $options = null);

    /**
     * Unregister.
     *
     * @param string $procedureName
     *
     * @return PromiseInterface
     */
    public function unregister($procedureName);

    /**
     * Call.
     *
     * @param string      $procedureName
     * @param array|mixed $arguments
     * @param array|mixed $argumentsKw
     * @param array|mixed $options
     *
     * @return PromiseInterface
     */
    public function call($procedureName, $arguments = null, $argumentsKw = null, $options = null);

    /**
     * @param int $sessionId
     */
    public function setSessionId($sessionId);

    /**
     * @return int the Session Id
     */
    public function getSessionId();

    /**
     * @param $msg
     */
    public function sendMessage($msg);
}
