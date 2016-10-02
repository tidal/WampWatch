<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Model\Factory;

use Guzzle\Common\Exception\RuntimeException;
use Tidal\WampWatch\Model\Contract\RouterInterface;
use Tidal\WampWatch\Model\Contract\RealmInterface;
use Tidal\WampWatch\Model\Contract\SessionInterface;
use Tidal\WampWatch\Model\Connection;

class ConnectionFactory
{
    private $router;

    private $realm;

    private $session;

    private function __construct(RouterInterface $router,
                                 RealmInterface $realm = null,
                                 SessionInterface $session = null
    ) {
        $this->router = $router;
        $this->realm = $realm;
        $this->session = $session;
    }

    /**
     * @param RouterInterface $router
     *
     * @return ConnectionFactory
     */
    public static function get(RouterInterface $router)
    {
        return new self($router);
    }

    /**
     * @param RealmInterface $realm
     *
     * @return ConnectionFactory
     */
    public function select(RealmInterface $realm)
    {
        return new self($this->router, $realm);
    }

    /**
     * @param SessionInterface $session
     *
     * @return ConnectionFactory
     */
    public function establish(SessionInterface $session)
    {
        return new self($this->router, $this->realm, $session);
    }

    /**
     * @return Connection
     */
    public function create()
    {
        if (!isset($this->realm)) {
            throw new RuntimeException('No realm set.');
        }

        return new Connection($this->router, $this->realm, $this->session);
    }
}
