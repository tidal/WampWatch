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


namespace Tidal\WampWatch\Model\EventSourcing\Realm;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Tidal\WampWatch\Model\Router as RouterModel;
use Tidal\WampWatch\Model\Realm as RealmModel;
use Tidal\WampWatch\Model\Contract\RealmInterface;
use Tidal\WampWatch\Model\Contract\RouterInterface;
use Tidal\WampWatch\Model\EventSourcing\Router\Event\RouterIsConnecting;
use Tidal\WampWatch\Model\EventSourcing\Router\Event\RouterIsConnected;


class Realm extends EventSourcedAggregateRoot
{

    /**
     * @var RouterInterface
     */
    private $routerEntity;

    /**
     * @var RealmInterface
     */
    private $realmEntity;

    private function __construct(RealmInterface $realm)
    {

        $this->apply(new RouterIsConnecting($routerEntity->getUri(), $routerEntity->getRealm()));
    }


    /**
     * @param RouterInterface $routerEntity
     * @param RealmInterface  $realm
     *
     * @return Router
     */
    public static function connectRealm(RouterInterface $routerEntity, RealmInterface $realm)
    {
        $router = new Router($routerEntity, $realm);
        return $router;
    }

    /**
     * Every aggregate root will expose its id.
     *
     * @return string
     */
    public function getAggregateRootId()
    {
        return $this->name;
    }


    public function applyRouterIsConnectingEvent(RouterIsConnecting $event)
    {
        $this->jobSeekerId = $event->jobSeekerId;
    }

}