<?php


/*
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Thruway\Peer\Client;
use Tidal\WampWatch\Adapter\Thruway\ClientSession as Adapter;
use Tidal\WampWatch\SessionMonitor;
use Tidal\WampWatch\Adapter\React\PromiseFactory;

/**
 * Class InternalClient.
 */
class InternalSessionMonitor extends Client
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('realm1');
    }

    /**
     * @param \Thruway\ClientSession                $session
     * @param \Thruway\Transport\TransportInterface $transport
     */
    public function onSessionStart($session, $transport)
    {
        $sessionMonitor = new SessionMonitor(new Adapter($session, new PromiseFactory()));


        $sessionMonitor->on('list', function ($l) {
            echo PHP_EOL . "LIST: " . PHP_EOL;
            print_r($l);
        });

        $sessionMonitor->on('join', function ($l) {
            echo PHP_EOL . "JOIN: " . PHP_EOL;
            print_r($l);
        });

        $sessionMonitor->on('leave', function ($l) {
            echo PHP_EOL . "LEAVE: " . PHP_EOL;
            print_r($l);
        });

        $sessionMonitor->on('error', function ($l) {
            echo PHP_EOL . "ERROR: " . PHP_EOL;
            print_r($l);
        });

        $sessionMonitor->start();

        echo PHP_EOL . "******** SESSION MONITOR STARTED ********" . PHP_EOL;
    }

    public function onMessage(Thruway\Transport\TransportInterface $transport, Thruway\Message\Message $msg)
    {
        echo PHP_EOL . "";
        //print_r($msg);
        parent::onMessage($transport, $msg);
    }
}
