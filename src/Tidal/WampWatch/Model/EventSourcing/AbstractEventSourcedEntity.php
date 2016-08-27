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


namespace Tidal\WampWatch\Model\EventSourcing\Event;


use Broadway\EventSourcing\EventSourcedEntity as AbstractEntity;
use Tidal\WampWatch\Model\EventSourcing\Event\DomainEvent;
use Tidal\WampWatch\Model\EventSourcing\Event\DomainEventInterface;


abstract class AbstractEventSourcedEntity extends AbstractEntity
{

    private $eventHandler = [];

    /**
     * @var RealmInterface
     */
    private $entityInstance;

    private $events = [];

    private $domainEvent;

    private function __construct($entityInstance)
    {
        $this->entityInstance = $entityInstance;
    }

    /**
     * Handles event if capable.
     *
     * @param $event
     */
    protected function handle($event)
    {
        parent::handle($event);

        if($event instanceof DomainEventInterface){
            $this->handleNamedEvent($event);
        }

    }

    private function handleNamedEvent(DomainEventInterface $event)
    {
        if($event->getScope() !== self::class){

            return;
        }

        if(!$this->hasEvent($event->getName())){

            return;
        }

        $this->getEvent($event->getName())->publish($event->getData());
    }

    public function listen(EventInterface $event, callable $callback)
    {
        if(!$this->hasEvent($event->getName())){
            return;
        }

        $this->getEvent($event->getName());
    }

    protected function exposeEvent($name)
    {
        return $this->events[$name] = $this->getDomainEvent()->name($name);
    }

    protected function exposeEvents(array $names)
    {
        foreach ($names as  $name){
            $this->exposeEvent((string) $name);
        }
    }


    /**
     * @param string $name
     *
     * @return DomainEventInterface
     */
    protected function getEvent($name)
    {
        if(!$this->hasEvent($name)){
            throw new \OutOfBoundsException("No event '$name' exposed.");
        }

        return $this->events[$name];
    }
    protected function hasEvent($name)
    {
        return isset($this->events[$name]);
    }

    /**
     * @return DomainEventInterface
     */
    private function getDomainEvent()
    {
        return isset($this->domainEvent)
            ? $this->domainEvent
            : $this->domainEvent = DomainEvent::bind(self::class);
    }
}