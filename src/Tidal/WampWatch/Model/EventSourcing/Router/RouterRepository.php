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



namespace Tidal\WampWatch\Model\EventSourcing\Router;


use Broadway\EventStore\EventStoreInterface;
use Broadway\EventHandling\EventBusInterface;
use Broadway\EventSourcing\AggregateFactory\PublicConstructorAggregateFactory;

use Tidal\WampWatch\Model\EventSourcing\Repository\EventSourcingRepository;



class Repository extends EventSourcingRepository
{
    public function __construct(EventStoreInterface $eventStore, EventBusInterface  $eventBus)
    {
        parent::__construct($eventStore, $eventBus, 'Router', new PublicConstructorAggregateFactory());
    }
}