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
use Tidal\WampWatch\Adapter\React\PromiseAdapter;
use Tidal\WampWatch\Behavior\Async\MakesDeferredPromisesTrait;
use Tidal\WampWatch\Behavior\Async\MakesPromisesTrait;
use Tidal\WampWatch\Async\DeferredInterface;

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
    use EventEmitterTrait,
        MakesPromisesTrait,
        MakesDeferredPromisesTrait;

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
     * @return PromiseAdapter
     */
    public function subscribe($topicName, callable $callback, $options = null)
    {
        $this->on($topicName, $callback);

        $this->subscriptions[$topicName] = static::createDeferredAdapter();
        $this->subscribing[$topicName] = new SubscribeMessage(
            count($this->subscriptions),
            (object) $options,
            $topicName
        );

        return $this->subscriptions[$topicName]->promise();
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
     * @return PromiseAdapter
     */
    public function publish($topicName, $arguments = null, $argumentsKw = null, $options = null)
    {
        $this->publications[$topicName] = static::createDeferredAdapter();
        $this->publishing[$topicName] = new PublishMessage(
            count($this->publishing),
            $options,
            $topicName,
            $arguments,
            $argumentsKw
        );

        return $this->publications[$topicName]->promise();
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

    /**
     * @param string $topicName
     * @param mixed  $error
     * @param int    $requestId
     */
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
     * @return PromiseAdapter
     */
    public function register($procedureName, callable $callback, $options = null)
    {
        $this->procedures[$procedureName] = $callback;

        $this->registrations[$procedureName] = static::createDeferredAdapter();
        $this->registering[$procedureName] = new RegisterMessage(
            count($this->registering),
            $options,
            $procedureName
        );

        return $this->registrations[$procedureName]->promise();
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
     * @return PromiseAdapter
     */
    public function unregister($procedureName)
    {
        $this->unregistrations[$procedureName] = static::createDeferredAdapter();

        return $this->unregistrations[$procedureName]->promise();
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
     * @return PromiseAdapter
     */
    public function call($procedureName, $arguments = null, $argumentsKw = null, $options = null)
    {
        $this->calls[$procedureName] = $this->createDeferredAdapter();
        $this->calling[$procedureName] = new CallMessage(
            count($this->calling),
            $options,
            $procedureName,
            $arguments,
            $argumentsKw
        );

        return $this->calls[$procedureName]->promise();
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
        unset($this->calls[$procedureName]);

        $futureResult->resolve($result);
    }

    /**
     * @param string $procedureName
     * @param mixed  $error
     */
    public function failCall($procedureName, $error)
    {
        if (!isset($this->calls[$procedureName])) {
            throw new UnknownProcedureException($procedureName);
        }

        /* @var $futureResult Deferred */
        $futureResult = $this->calls[$procedureName];
        unset($this->calls[$procedureName]);

        $futureResult->reject($error);
    }

    /**
     * @param string $procedureName
     *
     * @return bool
     */
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
     *
     * @return mixed
     */
    public function sendMessage($msg)
    {
        return $msg;
    }

    /**
     * @param callable $canceller
     *
     * @return DeferredInterface
     */
    private function createDeferredAdapter(callable $canceller = null)
    {
        $canceller = $canceller ?: function () {
        };

        return $this->getDeferredFactory()->create($canceller);
    }
}
