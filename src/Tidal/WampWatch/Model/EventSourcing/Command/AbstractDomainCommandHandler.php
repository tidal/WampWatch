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
use Tidal\WampWatch\Model\EventSourcing\Repository\RepositoryInterface;


abstract class AbstractDomainCommandHandler extends CommandHandling\CommandHandler implements CommandHandlerInterface
{

    use DomainCommandHandlerTrait;

    private $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function handle($command)
    {
        return $this->handleCommand($command);
    }

    public function getRepository()
    {
        return $this->repository;
    }

}