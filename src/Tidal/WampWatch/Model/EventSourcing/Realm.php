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


namespace Tidal\WampWatch\Model\EventSourcing;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Tidal\WampWatch\Model\Router as RouterModel;
use Tidal\WampWatch\Model\Realm as RealmModel;
use Tidal\WampWatch\Model\Contract\RealmInterface;
use Tidal\WampWatch\Model\Contract\RouterInterface;
use Tidal\WampWatch\Model\EventSourcing\Router\Event\RouterIsConnecting;
use Tidal\WampWatch\Model\EventSourcing\Router\Event\RouterIsConnected;
use Tidal\WampWatch\Model\EventSourcing\AbstractEventSourcedAggregateRoot;

class Realm extends AbstractEventSourcedAggregateRoot
{

    const EVENT_CREATED = 'created';
    const EVENT_CONNECTING = 'connecting';
    const EVENT_CONNECTED = 'connected';
    public $name;
    /**
     * @var RouterInterface
     */
    private $routerEntity;
    /**
     * @var RealmInterface
     */
    private $realmEntity;

    private function __construct(RealmInterface $realm = null)
    {
        $this->exposeEvents([
            self::EVENT_CREATED,
            self::EVENT_CONNECTING,
            self::EVENT_CONNECTED
        ]);

        $this->realmEntity = $realm;

        //$event = $this->getEvent(self::EVENT_CREATED)->publish($this);
        //$this->apply($event);
    }


    /**
     * @param RealmInterface $realm
     *
     * @return Realm
     */
    public static function create(RealmInterface $realm = null)
    {
        return new Realm($realm);
    }

    public function giveName($name)
    {
        $this->name = $name;
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


}