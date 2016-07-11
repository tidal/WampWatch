<?php

require_once realpath(__DIR__ . "/..") . "/bootstrap.php";
require_once 'InternalSessionMonitor.php';

/*
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


use Thruway\Peer\Router;
use Thruway\Transport\RatchetTransportProvider;

$router = new Router();
$router->registerModule(new RatchetTransportProvider("127.0.0.1", 9999));
$router->addInternalClient(new \InternalSessionMonitor());
$router->start();