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
        $sessionMonitor = new SessionMonitor(new Adapter($session));


        $sessionMonitor->on('list', function ($l) {
            echo "\nLIST: \n";
            print_r($l);
        });

        $sessionMonitor->on('join', function ($l) {
            echo "\nJOIN: \n";
            print_r($l);
        });

        $sessionMonitor->on('leave', function ($l) {
            echo "\nLEAVE: \n";
            print_r($l);
        });

        $sessionMonitor->on('error', function ($l) {
            echo "\nERROR: \n";
            print_r($l);
        });

        $sessionMonitor->start();

        echo "\n******** SESSION MONITOR STARTED ********\n";
    }

    public function onMessage(Thruway\Transport\TransportInterface $transport, Thruway\Message\Message $msg)
    {
        echo "\n";
        //print_r($msg);
        parent::onMessage($transport, $msg);
    }
}
