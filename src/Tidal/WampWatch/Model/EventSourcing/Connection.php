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


use Broadway\EventSourcing\EventSourcedEntity;
use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Tidal\WampWatch\Model\Router as RouterModel;
use Tidal\WampWatch\Model\Realm as RealmModel;
use Tidal\WampWatch\Model\Contract\RealmInterface;
use Tidal\WampWatch\Model\Contract\RouterInterface;
use Tidal\WampWatch\Model\Contract\ConnectionInterface;
use Tidal\WampWatch\Model\EventSourcing\Router\Event\RouterIsConnecting;
use Tidal\WampWatch\Model\EventSourcing\Router\Event\RouterIsConnected;

use Tidal\WampWatch\Model\EventSourcing\Connection\Event\ConnectionIsCreated;

use Tidal\WampWatch\Model\EventSourcing\Realm;
use Tidal\WampWatch\Model\EventSourcing\AbstractEventSourcedEntity;


class Connection extends AbstractEventSourcedEntity
{


    const EVENT_CREATED = 'created';
    const EVENT_ESTABLISHING = 'establishing';
    const EVENT_ESTABLISHED = 'established';
    public $uri;
    public $realm;
    /**
     * @var ConnectionInterface
     */
    private $connectionEntity;
    /**
     * @var RealmInterface
     */
    private $realmEntity;

    private function __construct(ConnectionInterface $connection)
    {
        $this->exposeEvents([
            self::EVENT_CREATED,
            self::EVENT_ESTABLISHING,
            self::EVENT_ESTABLISHED
        ]);

        $this->connectionEntity = $connection;

        $event = $this->getEvent(self::EVENT_CREATED)->publish($this);
        $this->apply($event);
    }

    /**
     * @param ConnectionInterface $connection
     *
     * @return Connection
     */
    public static function create(ConnectionInterface $connection)
    {
        return new Connection($connection);
    }

    /**
     * Every aggregate root will expose its id.
     *
     * @return string
     */
    public function getAggregateRootId()
    {
        return $this->uri . ':' . $this->realm;
    }

    public function applyConnectionIsCreated(ConnectionIsCreated $event)
    {
        $this->uri = $event->uri;
        $this->realm = $event->realm;
    }

}