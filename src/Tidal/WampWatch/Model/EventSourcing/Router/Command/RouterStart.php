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
namespace Tidal\WampWatch\Model\EventSourcing\Router\Command;


use Tidal\WampWatch\Model\EventSourcing\Command\DomainCommand;
use Tidal\WampWatch\Model\EventSourcing\Router;


class RouterStart extends DomainCommand
{

    const COMMAND_NAME = 'router.start';

    public static function create($name = self::COMMAND_NAME)
    {
        return parent::create($name)->bind(Router::class);
    }

}