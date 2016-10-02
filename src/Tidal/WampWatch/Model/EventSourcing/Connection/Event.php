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


namespace Tidal\WampWatch\Model\EventSourcing\Connection;

use Guzzle\Common\Exception\RuntimeException;
use Tidal\WampWatch\Model\EventSourcing\Event\EventInterface;
use Tidal\WampWatch\Model\EventSourcing\Event\EventTrait;

use Tidal\WampWatch\Model\EventSourcing\Connection\ConnectionTrait;

class Event
{

    use ConnectionTrait;
    use EventTrait;

    public function __construct($uri, $realm, $session = null, $name = '')
    {
        $this->initConnection($uri, $realm, $session);

        if ((string)$name !== '') {
            $this->eventName = $name;
        }
    }

    /**
     * @param $name
     *
     * @return Event
     */
    public function name($name)
    {
        if ($this->eventName !== '') {
            throw  new RuntimeException("Event has already be named.");
        }

        return new self($this->uri, $this->realm, $this->session, (string)$name);
    }


}