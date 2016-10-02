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


namespace Tidal\WampWatch\Model\EventSourcing\Repository;

use Broadway\EventSourcing\EventSourcingRepository as BaseRepository;
use Broadway\EventHandling\EventBusInterface;
use Broadway\EventSourcing\AggregateFactory\AggregateFactoryInterface;
use Broadway\EventStore\EventStoreInterface;

abstract class AbstractEventSourcingRepository extends BaseRepository implements RepositoryInterface
{
    public function __construct(EventStoreInterface $eventStore,
                                EventBusInterface $eventBus,
                                AggregateFactoryInterface $aggregateFactory,
                                array $eventStreamDecorators = [])
    {
        parent::__construct($eventStore, $eventBus, static::getEntityClass(), $aggregateFactory, $eventStreamDecorators);
    }

    /**
     * @return string fully qualified class name
     */
    abstract static function getEntityClass();
}