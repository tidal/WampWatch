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


namespace Tidal\WampWatch\Model\EventSourcing\Connection;

use Broadway\CommandHandling\CommandHandler as AbstractCommandHandler;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\Repository\RepositoryInterface;


class CommandHandler extends AbstractCommandHandler
{

    /**
     * @var RepositoryInterface
     */
    private $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle($command)
    {
        $method = $this->getHandleMethod($command);

        if (!method_exists($this, $method)) {
            return;
        }

        $this->$method($command);
    }

}