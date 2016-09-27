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
use Broadway\EventSourcing\AggregateFactory\NamedConstructorAggregateFactory;
use Broadway\Domain\AggregateRoot as AggregateRootInterface;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Tidal\WampWatch\Model\EventSourcing\Repository\AbstractEventSourcingRepository;
use Tidal\WampWatch\Model\EventSourcing\Router;


class RouterRepository extends AbstractEventSourcingRepository
{

    /**
     * RouterRepository constructor.
     *
     * @param \Broadway\EventStore\EventStoreInterface  $eventStore
     * @param \Broadway\EventHandling\EventBusInterface $eventBus
     */
    public function __construct(EventStoreInterface $eventStore, EventBusInterface $eventBus)
    {
        parent::__construct($eventStore, $eventBus, new NamedConstructorAggregateFactory('create'));
    }

    /**
     * @param mixed $id
     * @return Router
     */
    public function load($id)
    {
        return parent::load($id);
    }

    public function save(AggregateRootInterface $aggregate)
    {
        if (!is_a($aggregate, static::getEntityClass())) {
            throw new InvalidArgumentException("AggregateRoot must be instance of " . static::getEntityClass());
        }

        parent::save($aggregate);
    }

    /**
     * {@inheritDoc}
     */
    static function getEntityClass()
    {
        return Router::class;
    }
}