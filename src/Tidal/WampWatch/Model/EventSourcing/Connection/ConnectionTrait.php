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

use Tidal\WampWatch\Model\EventSourcing\ValueObject\ValueObjectTrait;

trait ConnectionTrait
{

    use ValueObjectTrait;

    public $uri;

    public $realm;

    public $session;

    public function initConnection($uri, $realm, $session = null)
    {
        $this->uri = $uri;
        $this->realm = $realm;
        $this->session = $session;
    }
}