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

/**
 * Created by PhpStorm.
 * User: Timo
 * Date: 25.07.2016
 * Time: 02:54
 */

namespace Tidal\WampWatch\Model\EventSourcing\Connection;


interface ConnectionInterface
{
    public $uri;

    public $realm;

}