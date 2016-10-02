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

use Tidal\WampWatch\Model\EventSourcing\Action\DomainActionInterface;
use Tidal\WampWatch\Model\EventSourcing\ValueObject\ValueObjectInterface;


interface DomainEventInterface extends EventInterface, DomainActionInterface
{

    /**
     * @param ValueObjectInterface $data
     *
     * @return DomainEventInterface
     */
    public function publish(ValueObjectInterface $data);


}