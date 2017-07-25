<?php

require_once realpath(__DIR__.'/..').'/bootstrap.php';

/*
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


use Psr\Log\NullLogger;
use Thruway\ClientSession;
use Thruway\Connection;
use Thruway\Logging\Logger;
use Tidal\WampWatch\Adapter\Thruway\ClientSession as Adapter;
use Tidal\WampWatch\SessionMonitor;
use Tidal\WampWatch\Adapter\React\PromiseFactory;

Logger::set(new NullLogger());

$connection = new Connection(
    [
        'realm' => 'realm1',
        'url'   => 'ws://127.0.0.1:8080/ws',
    ]
);

$connection->on('open', function (ClientSession $session) use ($connection, &$timer) {
    $sessionMonitor = SessionMonitor::create(new Adapter($session, new PromiseFactory()));

    echo PHP_EOL . "******** SESSION MONITOR STARTED ********" . PHP_EOL;
    $sessionMonitor->on('start', function ($l) {
        echo PHP_EOL . "START: " . PHP_EOL;
        print_r($l);
    });

    $sessionMonitor->on('list', function ($l) {
        echo PHP_EOL . "LIST: " . PHP_EOL;
        print_r($l);
    });

    $sessionMonitor->on('join', function ($sessionData) use ($sessionMonitor) {
        echo PHP_EOL . "JOIN: {$sessionData->session}" . PHP_EOL;

        $sessionMonitor->getSessionIds()->then(function (array $sessions) {

            echo "SESSIONS : " . count($sessions) . PHP_EOL;

        });
    });

    $sessionMonitor->on('leave', function ($sessionId) use ($sessionMonitor) {
        echo PHP_EOL . "LEAVE: $sessionId" . PHP_EOL;
        $sessionMonitor->getSessionIds()->then(function (array $sessions) {

            echo "SESSIONS : " . count($sessions) . PHP_EOL;

        });
    });

    $sessionMonitor->on('error', function ($l) {
        echo PHP_EOL . "ERROR: " . PHP_EOL;
        print_r($l);
    });

    $sessionMonitor->start();
}
);

$connection->on('close', function ($reason) use (&$timer) {
    echo "The connected has closed with reason: {$reason}" . PHP_EOL;
});

$connection->on('error', function ($reason) {
    echo "The connected has closed with error: {$reason}" . PHP_EOL;
});

$connection->open();
