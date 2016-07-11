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

Logger::set(new NullLogger());

$connection = new Connection(
    [
        'realm' => 'realm1',
        'url'   => 'ws://127.0.0.1:9999/ws',
    ]
);

$connection->on('open', function (ClientSession $session) use ($connection, &$timer) {
    $sessionMonitor = new SessionMonitor(new Adapter($session));


    $sessionMonitor->on('start', function ($l) {
        echo "\n******** SESSION MONITOR STARTED ********\n";
        echo "\nSTART: \n";
        print_r($l);
    });

    $sessionMonitor->on('list', function ($l) {
        echo "\nLIST: \n";
        print_r($l);
    });

    $sessionMonitor->on('join', function ($sessionData) {
        echo "\nJOIN: {$sessionData->session}\n";
    });

    $sessionMonitor->on('leave', function ($sessionId) {
        echo "\nLEAVE: $sessionId\n";
    });

    $sessionMonitor->on('error', function ($l) {
        echo "\nERROR: \n";
        print_r($l);
    });

    $sessionMonitor->start();
}
);

$connection->on('close', function ($reason) use (&$timer) {
    echo "The connected has closed with reason: {$reason}\n";
});

$connection->on('error', function ($reason) {
    echo "The connected has closed with error: {$reason}\n";
});

$connection->open();
