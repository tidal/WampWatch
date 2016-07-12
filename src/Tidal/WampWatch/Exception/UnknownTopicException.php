<?php


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