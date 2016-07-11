<?php
namespace Tidal\WampWatch\Stub;


/*
 * Copyright 2016 Timo Michna.
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

use Thruway\Message\SubscribedMessage;
use Thruway\Message\PublishedMessage;
use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;
use React\Promise\Deferred;

use Tidal\WampWatch\ClientSessionInterface;

/**
 * Class ClientSessionStub
 *
 * @package Tidal\WampWatch\Stub
 *
 * !!! WARNING !!!!
 * This Class should only be used for testing or demos.
 *
 */
class ClientSessionStub implements ClientSessionInterface, EventEmitterInterface
{

    use EventEmitterTrait;

    protected $sessionId;

    protected $callRequests = [];

    protected $subscriptions = [];

    protected $publications = [];



    /**
     * Subscribe.
     *
     * @param string   $topicName
     * @param callable $callback
     * @param          $options array
     * @return \React\Promise\Promise
     */
    public function subscribe($topicName, callable $callback, $options = null)
    {
        $this->on($topicName, $callback);

        $futureResult = new Deferred();

        $this->subscriptions[$topicName] = $futureResult;

        return $futureResult->promise();
    }

    /**
     * @param $topicName
     * @param $requestId
     * @param $sessionId
     * @throws \RuntimeException
     */
    public function completeSubscription($topicName, $requestId = 1, $sessionId = 1)
    {
        if (!isset($this->subscriptions[$topicName])) {
            throw new \RuntimeException("No subscription to topic '$topicName' initiated.");
        }

        /* @var $futureResult Deferred */
        $futureResult = $this->subscriptions[$topicName];
        $result = new SubscribedMessage($requestId, $sessionId);

        $futureResult->resolve($result);

    }


    /**
     * Publish.
     *
     * @param string      $topicName
     * @param array|mixed $arguments
     * @param array|mixed $argumentsKw
     * @param array|mixed $options
     * @return \React\Promise\Promise
     */
    public function publish($topicName, $arguments = null, $argumentsKw = null, $options = null)
    {
        $futureResult = new Deferred();

        $this->publications[$topicName] = $futureResult;

        return $futureResult->promise();
    }

    public function confirmPublication($topicName, $requestId = 1, $publicationId = 1)
    {
        if (!isset($this->publications[$topicName])) {
            throw new \RuntimeException("No publication to topic '$topicName' initiated.");
        }

        $futureResult = $this->publications[$topicName];
        $result = new PublishedMessage($requestId, $publicationId);

        $futureResult->resolve($result);
    }


    /**
     * Register.
     *
     * @param string      $procedureName
     * @param callable    $callback
     * @param array|mixed $options
     * @return \React\Promise\Promise
     */
    public function register($procedureName, callable $callback, $options = null)
    {

        $this->on($procedureName, $callback);

        $futureResult = new Deferred();

        $this->callRequests[$procedureName] = $futureResult;


        return $futureResult->promise();

    }

    /**
     * Process ResultMessage
     *
     * @param string    $procedureName
     * @param \stdClass $result
     */
    public function processResult($procedureName, $result)
    {
        if (isset($this->callRequests[$procedureName])) {
            /* @var $futureResult Deferred */
            $futureResult = $this->callRequests[$procedureName];

            $futureResult->notify($result);
        }
    }


    /**
     * Unregister.
     *
     * @param string $procedureName
     *
     * @return \React\Promise\Promise|false
     */
    public function unregister($procedureName)
    {

        $futureResult = new Deferred();

        return $futureResult->promise();

    }

    /**
     * Call.
     *
     * @param string      $procedureName
     * @param array|mixed $arguments
     * @param array|mixed $argumentsKw
     * @param array|mixed $options
     * @return \React\Promise\Promise
     */
    public function call($procedureName, $arguments = null, $argumentsKw = null, $options = null)
    {

        //$this->on($procedureName, $callback);

        $futureResult = new Deferred();

        return $futureResult->promise();

    }

    /**
     * @param int $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return int the Session Id
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Generate a unique id for sessions and requests
     *
     * @return mixed
     */
    public static function getUniqueId()
    {
        $filter = 0x1fffffffffffff; // 53 bits
        $randomBytes = openssl_random_pseudo_bytes(8);
        list($high, $low) = array_values(unpack("N2", $randomBytes));

        return abs(($high << 32 | $low) & $filter);
    }


}