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


trait DomainEventEmitterTrait
{

    private $events = [];

    private $domainEvent;

    protected function exposeEvents(array $names)
    {
        foreach ($names as $name) {
            $this->exposeEvent((string)$name);
        }
    }

    protected function exposeEvent($name)
    {
        return $this->events[$name] = $this->getDomainEvent()->name($name);
    }

    /**
     * @return DomainEventInterface
     */
    private function getDomainEvent()
    {
        return isset($this->domainEvent)
            ? $this->domainEvent
            : $this->domainEvent = DomainEvent::create()->bind(static::class);
    }

    private function handleNamedEvent(DomainEventInterface $event)
    {
        if ($event->getScope() !== self::class) {

            return;
        }

        if (!$this->hasEvent($event->getName())) {

            return;
        }

        $this->getEvent($event->getName())->publish($event->getData());
    }

    protected function hasEvent($name)
    {
        return isset($this->events[$name]);
    }

    /**
     * @param string $name
     *
     * @return DomainEventInterface
     */
    protected function getEvent($name)
    {
        if (!$this->hasEvent($name)) {
            throw new \OutOfBoundsException("No event '$name' exposed.");
        }

        return $this->events[$name];
    }
}