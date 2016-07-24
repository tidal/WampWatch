<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Exception;

/**
 * Class NoSuchProcedureException.
 *
 * Thrown when a router does not support a given meta topic
 */
class NotAuthorizedException extends \OutOfBoundsException
{
    const ERROR_RESPONSE = 'wamp.error.not_authorized';

    /**
     * @var string name of the topic
     */
    protected $topicName;

    /**
     * @param string $topicName
     * @param        string the router error message
     */
    public function __construct($topicName, $errorMsg = 'session is not authorized')
    {
        $this->setTopicName($topicName);

        parent::__construct($errorMsg);
    }

    /**
     * @param string $topicName
     */
    protected function setTopicName($topicName)
    {
        $this->topicName = $topicName;
    }

    /**
     * @return string the topic name
     */
    public function getTopicName()
    {
        return $this->topicName;
    }

    /**
     * Checks if a router response is a 'no_such_topic' error.
     *
     * @param $errorResponse
     *
     * @return bool
     */
    public static function isNotAuthrorizedError($errorResponse)
    {
        return $errorResponse == self::ERROR_RESPONSE;
    }
}
