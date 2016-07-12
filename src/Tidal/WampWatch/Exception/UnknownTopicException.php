<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Exception;

class UnknownTopicException extends \OutOfBoundsException
{
    /**
     * @var string name of the topic
     */
    protected $topicName;

    /**
     * @param string $topicName
     */
    public function __construct($topicName)
    {
        $this->setTopicName($topicName);

        parent::__construct("unknown topic '$topicName'");
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
}
