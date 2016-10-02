<?php
/**
 *
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace Tidal\WampWatch\Model\Property\Object;

use Tidal\WampWatch\Model\Contract;

trait HasTopicTrait
{
    /**
     * @var Contract\TopicInterface
     */
    private $topic;

    /**
     * @return Contract\TopicInterface
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @param Contract\TopicInterface $topic
     */
    private function setTopic(Contract\TopicInterface $topic)
    {
        $this->topic = $topic;
    }


}