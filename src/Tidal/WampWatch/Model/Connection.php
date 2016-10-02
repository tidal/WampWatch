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


namespace Tidal\WampWatch\Model;

use Tidal\WampWatch\Model\Contract;
use Tidal\WampWatch\Model\Property\Object\HasRouterTrait;
use Tidal\WampWatch\Model\Property\Object\HasRealmTrait;
use Tidal\WampWatch\Model\Property\Object\HasSessionTrait;

class Connection implements Contract\ConnectionInterface
{
    use HasRouterTrait;
    use HasRealmTrait;
    use HasSessionTrait;

    public function __construct(Contract\RouterInterface $router, Contract\RealmInterface $realm, Contract\SessionInterface $session = null)
    {
        $this->setRouter($router);
        $this->setRealm($realm);
        if ($session !== null) {
            $this->confirm($session);
        }
    }

    public function confirm(Contract\SessionInterface $session)
    {
        $this->setSession($session);
    }

}