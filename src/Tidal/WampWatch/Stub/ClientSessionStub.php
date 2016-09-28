<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Stub;

use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;
use React\Promise\Deferred;
use Thruway\Message\PublishedMessage;
use Thruway\Message\PublishMessage;
use Thruway\Message\SubscribedMessage;
use Thruway\Message\SubscribeMessage;
use Thruway\Message\RegisteredMessage;
use Thruway\Message\RegisterMessage;
use Thruway\Message\UnregisteredMessage;
use Thruway\Message\CallMessage;
use Thruway\Message\ErrorMessage;
use Tidal\WampWatch\ClientSessionInterface;
use Tidal\WampWatch\Exception\UnknownProcedureException;
use Tidal\WampWatch\Exception\UnknownTopicException;

/**
 * !!! WARNING !!!!
 * This Class should only be used for testing or demos.
 * It allows for testing client method calls but behaves differently to
 * real client session implementation in that it only stores one (the last)
 * subscription, registration etc. for a specific topic/procedure.
 */

/**
 * Class ClientSessionStub.
 */
class ClientSessionStub implements ClientSessionInterface, EventEmitterInterface
{
    use EventEmitterTrait;

    /**
     * @var int
     */
    protected $sessionId;

    /**
     * @var array
     */
    protected $subscriptions = [];

    /**
     * @var array
     */
    protected $subscribing = [];

    /**
     * @var array
     */
    protected $publications = [];

    /**
     * @var array
     */
    protected $publishing = [];

    /**
     * @var array
     */
    protected $registrations = [];

    /**
     * @var array
     */
    protected $registering = [];

    /**
     * @var array
     */
    protected $unregistrations = [];

    /**
     * @var array
     */
    protected $calls = [];

    /**
     * @var array
     */
    protected $calling = [];

    /**
     * @var array
     */
    protected $procedures = [];

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
        $this->on($topicName, $callback);

        $futureResult = new Deferred();

        $this->subscriptions[$topicName] = $futureResult;
        $this->subscribing[$topicName] = new SubscribeMessage(
            count($this->subscriptions),
            (object) $options,
            $topicName
        );

        return $futureResult->promise();
    }

    /**
     * Trigger a SUBSCRIBED message for given topic.
     *
     * @param $topicName
     * @param $requestId
     * @param $subscriptionId
     *
     * @throws UnknownTopicException if the topic is unknown
     */
    public function completeSubscription($topicName, $requestId = 1, $subscriptionId = 1)
    {
        if (!isset($this->subscriptions[$topicName])) {
            throw new UnknownTopicException($topicName);
        }

        /* @var $futureResult Deferred */
        $futureResult = $this->subscriptions[$topicName];
        $result = new SubscribedMessage($requestId, $subscriptionId);

        $futureResult->resolve($result);
    }

    public function hasSubscription($topicName)
    {
        return isset($this->subscriptions[$topicName]);
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
        $futureResult = new Deferred();

        $this->publications[$topicName] = $futureResult;
        $this->publishing[$topicName] = new PublishMessage(
            count($this->publishing),
            $options,
            $topicName,
            $arguments,
            $argumentsKw
        );

        return $futureResult->promise();
    }

    /**
     * Trigger a PUBLISHED message for given topic.
     *
     * @param string $topicName
     * @param int    $requestId
     * @param int    $publicationId
     *
     * @throws UnknownTopicException if the topic is unknown
     */
    public function confirmPublication($topicName, $requestId = 1, $publicationId = 1)
    {
        if (!isset($this->publications[$topicName])) {
            throw new UnknownTopicException($topicName);
        }

        $futureResult = $this->publications[$topicName];
        $result = new PublishedMessage($requestId, $publicationId);

        $futureResult->resolve($result);
    }

    public function failPublication($topicName, $error, $requestId = 1)
    {
        if (!isset($this->publications[$topicName])) {
            throw new UnknownTopicException($topicName);
        }

        $futureResult = $this->publications[$topicName];
        $result = new ErrorMessage($error, $requestId, new \stdClass(), $topicName);

        $futureResult->reject($result);
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
        $this->procedures[$procedureName] = $callback;

        $futureResult = new Deferred();

        $this->registrations[$procedureName] = $futureResult;
        $this->registering[$procedureName] = new RegisterMessage(
            count($this->registering),
            $options,
            $procedureName
        );

        return $futureResult->promise();
    }

    /**
     * Trigger a REGISTERED message for given procedure.
     *
     * @param string $procedureName
     * @param int    $requestId
     * @param int    $registrationId
     *
     * @throws UnknownProcedureException if the procedure is unknown
     */
    public function confirmRegistration($procedureName, $requestId = 1, $registrationId = 1)
    {
        if (!isset($this->registrations[$procedureName])) {
            throw new UnknownProcedureException($procedureName);
        }

        $futureResult = $this->registrations[$procedureName];
        $result = new RegisteredMessage($requestId, $registrationId);

        $futureResult->resolve($result);
    }

    /**
     * Triggers a call to a registered procedure and returns its result.
     *
     * @param string $procedureName
     * @param array  $args
     *
     * @throws UnknownProcedureException if the procedure is unknown
     *
     * @return mixed the procedure result
     */
    public function callRegistration($procedureName, array $args = [])
    {
        if (!isset($this->procedures[$procedureName])) {
            throw new UnknownProcedureException($procedureName);
        }

        $procedure = $this->procedures[$procedureName];

        return $procedure($args);
    }

    /**
     * Unregister.
     *
     * @param string $procedureName
     *
     * @return \React\Promise\PromiseInterface
     */
    public function unregister($procedureName)
    {
        $futureResult = new Deferred();

        $this->unregistrations[$procedureName] = $futureResult;

        return $futureResult->promise();
    }

    /**
     * Triggers a UNREGISTERED message for given procedure.
     *
     * @param string $procedureName
     * @param int    $requestId
     *
     * @throws UnknownProcedureException
     */
    public function confirmUnregistration($procedureName, $requestId = 1)
    {
        if (!isset($this->unregistrations[$procedureName])) {
            throw new UnknownProcedureException($procedureName);
        }

        $futureResult = $this->unregistrations[$procedureName];
        $result = new UnregisteredMessage($requestId);

        $futureResult->resolve($result);
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
        $futureResult = new Deferred();

        $this->calls[$procedureName] = $futureResult;
        $this->calling[$procedureName] = new CallMessage(
            count($this->calling),
            $options,
            $procedureName,
            $arguments,
            $argumentsKw
        );

        return $futureResult->promise();
    }

    /**
     * Process ResultMessage.
     *
     * @param string $procedureName
     * @param mixed  $result
     */
    public function respondToCall($procedureName, $result)
    {
        if (!isset($this->calls[$procedureName])) {
            throw new UnknownProcedureException($procedureName);
        }

        /* @var $futureResult Deferred */
        $futureResult = $this->calls[$procedureName];

        $futureResult->resolve($result);
    }

    public function failCall($procedureName, $error)
    {
        if (!isset($this->calls[$procedureName])) {
            throw new UnknownProcedureException($procedureName);
        }

        /* @var $futureResult Deferred */
        $futureResult = $this->calls[$procedureName];

        $futureResult->reject($error);
    }

    public function hasCall($procedureName)
    {
        return isset($this->calls[$procedureName]);
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
     * Generate a unique id for sessions and requests.
     *
     * @return mixed
     */
    public static function getUniqueId()
    {
        $filter = 0x1fffffffffffff; // 53 bits
        $randomBytes = openssl_random_pseudo_bytes(8);
        list($high, $low) = array_values(unpack('N2', $randomBytes));

        return abs(($high << 32 | $low) & $filter);
    }

    /**
     * @param $msg
     * @return mixed
     */
    public function sendMessage($msg)
    {
        return $msg;
    }
}
