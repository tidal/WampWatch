<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch;

interface ClientSessionInterface
{
    /**
     * Subscribe.
     *
     * @param string   $topicName
     * @param callable $callback
     * @param $options array
     */
    public function subscribe($topicName, callable $callback, $options = null);

    /**
     * Publish.
     *
     * @param string      $topicName
     * @param array|mixed $arguments
     * @param array|mixed $argumentsKw
     * @param array|mixed $options
     */
    public function publish($topicName, $arguments = null, $argumentsKw = null, $options = null);

    /**
     * Register.
     *
     * @param string      $procedureName
     * @param callable    $callback
     * @param array|mixed $options
     */
    public function register($procedureName, callable $callback, $options = null);

    /**
     * Unregister.
     *
     * @param string $procedureName
     *
     * @return \React\Promise\Promise|false
     */
    public function unregister($procedureName);

    /**
     * Call.
     *
     * @param string      $procedureName
     * @param array|mixed $arguments
     * @param array|mixed $argumentsKw
     * @param array|mixed $options
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
}
