<?php

require_once realpath(__DIR__.'/..').'/bootstrap.php';

/*
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Thruway\ClientSession;
use Thruway\Connection;
use Thruway\Logging\Logger;
use Psr\Log\NullLogger;

Logger::set(new NullLogger());

$timer = null;
$loop = React\EventLoop\Factory::create();
$connection = new Connection(
    [
        'realm' => 'realm1',
        'url'   => 'ws://127.0.0.1:8080/ws',
    ],
    $loop
);

$connection->on('open', function (ClientSession $session) use ($connection, $loop, &$timer) {
    echo PHP_EOL . "Session established: {$session->getSessionId()}" . PHP_EOL;
}
);

$connection->on('close', function ($reason) use ($loop, &$timer) {
    if ($timer) {
        $loop->cancelTimer($timer);
    }
    echo "The connected has closed with reason: {$reason}" . PHP_EOL;
});

$connection->on('error', function ($reason) {
    echo "The connected has closed with error: {$reason}" . PHP_EOL;
});

$connection->open();
