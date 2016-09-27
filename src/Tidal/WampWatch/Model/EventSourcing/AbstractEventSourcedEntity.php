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


use Broadway\EventSourcing\EventSourcedEntity as AbstractEntity;
use Tidal\WampWatch\Model\EventSourcing\Event\DomainEvent;
use Tidal\WampWatch\Model\EventSourcing\Event\DomainEventInterface;
use Tidal\WampWatch\Model\EventSourcing\Event\DomainEventEmitterTrait;
use Tidal\WampWatch\Model\EventSourcing\ValueObject\ValueObjectInterface;
use Tidal\WampWatch\Model\EventSourcing\ValueObject\ValueObjectTrait;


abstract class AbstractEventSourcedEntity extends AbstractEntity implements ValueObjectInterface
{

    use DomainEventEmitterTrait;
    use ValueObjectTrait;

    /**
     * @var RealmInterface
     */
    private $entityInstance;

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

}