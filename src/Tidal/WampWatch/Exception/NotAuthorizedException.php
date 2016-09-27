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

    const DEFAULT_ERROR_MSG = 'session is not authorized';

    /**
     * @var string name of the topic
     */
    protected $topicName;

    /**
     * @param string $topicName
     * @param string $errorMsg the router error message
     */
    public function __construct($topicName, $errorMsg = self::DEFAULT_ERROR_MSG)
    {
        $this->setTopicName($topicName);

        parent::__construct($errorMsg);
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

    /**
     * @return string the topic name
     */
    public function getTopicName()
    {
        return $this->topicName;
    }

    /**
     * @param string $topicName
     */
    protected function setTopicName($topicName)
    {
        $this->topicName = $topicName;
    }
}
