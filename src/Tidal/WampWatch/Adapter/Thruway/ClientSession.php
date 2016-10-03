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
use Tidal\WampWatch\Adapter\React\PromiseAdapter;
use Tidal\WampWatch\Behavior\Async\MakesPromisesTrait;
use Tidal\WampWatch\Adapter\React\PromiseFactory;

class ClientSession implements ClientSessionInterface
{
    use MakesPromisesTrait;

    /**
     * @var ThruwaySession
     */
    protected $thruwaySession;

    /**
     * ClientSession constructor.
     *
     * @param ThruwaySession $session
     * @param PromiseFactory $factory
     */
    public function __construct(ThruwaySession $session, PromiseFactory $factory)
    {
        $this->setThruwaySession($session);
        $this->setPromiseFactory($factory);
    }

    /**
     * @param ThruwaySession $session
     */
    public function setThruwaySession(ThruwaySession $session)
    {
        $this->thruwaySession = $session;
    }

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
        return $this->callAdaptee('subscribe', [$topicName, $callback, $options]);
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
        return $this->callAdaptee('publish', [$topicName, $arguments, $argumentsKw, $options]);
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
        return $this->callAdaptee('register', [$procedureName, $callback, $options]);
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
        return $this->callAdaptee('unregister', [$procedureName]);
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
        return $this->callAdaptee('call', [$procedureName, $arguments, $argumentsKw, $options]);
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

    /**
     * @param $msg
     */
    public function sendMessage($msg)
    {
        $this->thruwaySession->sendMessage($msg);
    }

    private function callAdaptee($command, array $arguments = [])
    {
        return $this->promiseFactory->createFromAdaptee(
            call_user_func_array([
                $this->thruwaySession,
                $command
            ], $arguments)
        );
    }
}
