<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Model\Property\Collection;

use Tidal\WampWatch\Model\Contract;
use Tidal\WampWatch\Model\Contract\Property\ObjectCollectionInterface;

/**
 * Class HasTopicsTrait.
 *
 * Important! Classes using this trait have to also use trait
 * Tidal\WampWatch\Model\Behavior\Property\HasCollectionsTrait
 * for this trait to work;
 */
trait HasTopicsTrait
{
    protected $topicsPropertyName = 'topics';

    /**
     * @var ObjectCollectionInterface
     */
    private $topics;

    public function addTopic(Contract\TopicInterface $topic)
    {
        $this->getTopics()->set($topic->getUri(), $topic);
    }

    /**
     * @return ObjectCollectionInterface
     */
    private function getTopics()
    {
        return $this->getCollection($this->topicsPropertyName);
    }

    /**
     * @param string $name
     *
     * @return ObjectCollectionInterface
     */
    abstract protected function getCollection($name);

    public function hasTopic($uri)
    {
        return $this->getTopics()->has($uri);
    }

    /**
     * @param string $uri
     *
     * @return Contract\TopicInterface
     */
    public function getTopic($uri)
    {
        return $this->getTopics()->get($uri);
    }

    /**
     * @param string $uri
     */
    public function removeTopic($uri)
    {
        $this->getTopics()->offsetUnset($uri);
    }

    /**
     * @return \Generator
     */
    public function listTopics()
    {
        foreach ($this->getTopics()->getIterator() as $uri => $topic) {
            yield $uri => $topic;
        }
    }

    /**
     * @param ObjectCollectionInterface $topics
     */
    private function setTopics(ObjectCollectionInterface $topics)
    {
        $this->initCollection($this->topicsPropertyName, $topics);
        $topics->setObjectConstrain(Contract\TopicInterface::class);
    }
}
