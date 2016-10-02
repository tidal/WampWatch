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


namespace Tidal\WampWatch\Model\EventSourcing\Factory;

use Tidal\WampWatch\Model\EventSourcing\Router\Router;
use Tidal\WampWatch\Model\Router as RouterModel;


class RouterFactory
{


    public function __construct()
    {

    }

    public function createFromEntity(RouterModel $entity)
    {
        //return new Router();
    }

}