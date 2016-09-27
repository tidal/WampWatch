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
use Tidal\WampWatch\Model\EventSourcing\Event\DomainEventEmitterTrait;
use Tidal\WampWatch\Model\EventSourcing\ValueObject\ValueObjectInterface;
use Tidal\WampWatch\Model\EventSourcing\ValueObject\ValueObjectTrait;
use Tidal\WampWatch\Model\EventSourcing\ValueObject\ValueObject;
use Tidal\WampWatch\Model\EventSourcing\Command\DomainCommandInterface;
use Tidal\WampWatch\Model\EventSourcing\Command\DomainCommandHandlerTrait;


abstract class AbstractEventSourcedAggregateRoot extends EventSourcedAggregateRoot implements ValueObjectInterface
{
    use DomainEventEmitterTrait;
    use DomainCommandHandlerTrait;
    use ValueObjectTrait;

    /**
     * @var mixed
     */
    private $entityInstance;

    /**
     * AbstractEventSourcedAggregateRoot constructor.
     *
     * @param mixed $entityInstance
     */
    private function __construct($entityInstance)
    {
        $this->entityInstance = $entityInstance;
    }

    /**
     * Handles event if capable.
     *
     * @param $event
     */
    public function handle($event)
    {
        parent::handle($event);

        if ($event instanceof DomainEventInterface) {
            $this->handleNamedEvent($event);
        }

        if ($event instanceof DomainCommandInterface) {
            $this->handleCommand($event);
        }

    }

    /**
     * @param string $eventName
     * @param array  $params
     *
     * @return mixed
     */
    public function publish($eventName, array $params)
    {
        return $this->getEvent($eventName)->publish(ValueObject::create($params));
    }

}