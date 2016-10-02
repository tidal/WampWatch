<?php

/**
 *
 *  * This file is part of the Tidal/WampWatch package.
 *  * (c) 2016 Timo Michna <timomichna/yahoo.de>
 *  *
 *  * For the full copyright and license information, please view the LICENSE
 *  * file that was distributed with this source code.
 *
 */
namespace Tidal\WampWatch\Model;


use Tidal\WampWatch\Model\Contract\SubscriptionInterface;
use Tidal\WampWatch\Model\Contract\SessionInterface;
use Tidal\WampWatch\Model\Contract\TopicInterface;
use Tidal\WampWatch\Model\Property\Scalar\HasIdTrait;
use Tidal\WampWatch\Model\Property\Object\HasSessionTrait;
use Tidal\WampWatch\Model\Property\Object\HasTopicTrait;


class Subscription implements Contract\SubscriptionInterface
{
    use HasIdTrait;
    use HasSessionTrait;
    use HasTopicTrait;

    public function __construct($id, SessionInterface $session, TopicInterface $topic)
    {
        $this->setId($id);
        $this->setSession($session);
        $this->setTopic($topic);
    }

}