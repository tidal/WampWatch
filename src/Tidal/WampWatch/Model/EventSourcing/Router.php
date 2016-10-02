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
use Tidal\WampWatch\Model\EventSourcing\ValueObject\ValueObject;
use Tidal\WampWatch\Model\EventSourcing\ValueObject\ValueObjectInterface;
use Tidal\WampWatch\Model\EventSourcing\Event\DomainEventInterface;

class Router extends AbstractEventSourcedAggregateRoot
{

    const EVENT_STARTED = 'router.started';
    const EVENT_CREATED = 'created';
    const EVENT_CONNECTING = 'connecting';
    const EVENT_CONNECTED = 'connected';
    const EVENT_REALM_EXPOSED = 'router.exposed.realm';
    public $uri;
    public $id;
    public $realms = [];
    /**
     * @var RouterInterface
     */
    private $routerEntity;

    private function __construct(RouterInterface $router = null)
    {
        $this->exposeEvents([
            self::EVENT_STARTED,
            self::EVENT_CREATED,
            self::EVENT_CONNECTING,
            self::EVENT_CONNECTED,
            self::EVENT_REALM_EXPOSED
        ]);

        $this->startListening();

        $this->routerEntity = $router;

        $event = $this->getEvent(self::EVENT_CREATED)->publish(ValueObject::create([]));
        $this->apply($event);

        echo "#############################################";
    }

    private function startListening()
    {
        $this->getEvent(self::EVENT_STARTED)->subscribe(function (DomainEventInterface $event) {
            $this->uri = $event->data->uri;
            echo "URI = '{$this->uri}'\n\n";
        });

        $this->getEvent(self::EVENT_REALM_EXPOSED)->subscribe(function (DomainEventInterface $event) {

            $data = $event->data;
            $realmName = (string)$data->realm;

            if ($event->data->uri !== $this->uri) {

                return;
            }
            if (array_key_exists($realmName, $this->realms)) {
                throw new \InvalidArgumentException("Realm {$realmName} already assigned to this router.");
            }

            echo "REALM = '{$realmName}'\n\n";

            $this->realms[$realmName] = Realm::create();
            $this->realms[$realmName]->giveName($realmName);
            //print_r($this->realms);
        });

    }

    public static function create()
    {
        return new self();
    }

    public function start($uri)
    {
        $router = new self();

        $this->publish(self::EVENT_STARTED, ["uri" => $uri]);

        return $this;
    }

    /**
     * @param string $realm
     * @return Realm
     */
    public function expose($realm)
    {
        return $this->publish(self::EVENT_REALM_EXPOSED, ["uri" => $this->uri, "realm" => $realm]);


    }

    /**
     * Every aggregate root will expose its id.
     *
     * @return string
     */
    public function getAggregateRootId()
    {
        return $this->uri;
    }

    protected function getChildEntities()
    {
        return $this->realms;
    }

}