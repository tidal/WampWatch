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


namespace Tidal\WampWatch\Model\EventSourcing\Command;

use Broadway\CommandHandling;
use Tidal\WampWatch\Model\CommandSourcing\Command\DomainCommandHandlerTrait;


abstract class AbstractDomainCommandHandler extends CommandHandling\CommandHandler implements CommandHandlerInterface
{

    use DomainCommandHandlerTrait;

    /**
     * {@inheritDoc}
     */
    public function handle($command)
    {
        parent::handle($command);


    }

}