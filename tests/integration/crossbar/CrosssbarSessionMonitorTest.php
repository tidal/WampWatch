<?php

namespace integration\crossbar;

require_once realpath(__DIR__ . "/../..") . "/bootstrap.php";
require_once __DIR__ . "/CrossbarTestingTrait.php";

use Thruway\ClientSession;
use Tidal\WampWatch\SessionMonitor;
use Tidal\WampWatch\Adapter\Thruway\ClientSession as Adapter;

class CrosssbarSessionMonitorTest extends \PHPUnit_Framework_TestCase
{
    use CrossbarTestingTrait;

    const REALM_NAME = 'realm1';

    const ROUTER_URL = 'ws://127.0.0.1:8080/ws';

    /**
     *
     */
    public function setup()
    {
        $this->setupConnection();
    }

    /**
     *
     */
    public function test_onstart()
    {

        $sessionIds = null;

        $this->connection->on('open', function (ClientSession $session) use (&$sessionIds) {

            $sessionMonitor = new SessionMonitor(new Adapter($session));

            $sessionMonitor->on('start', function ($ids) use (&$sessionIds, $sessionMonitor) {

                $sessionIds = $ids;

                $sessionMonitor->stop();
                $this->connection->close();
            });

            $sessionMonitor->start();


        });

        $this->connection->on('error', function ($reason) {
            echo "The connected has closed with error: {$reason}\n";
        });

        $this->connection->open();

        $this->assertTrue(is_array($sessionIds));


    }


    public function test_onjoin()
    {

        $this->connection->on('open', function (ClientSession $session) {

            $sessionMonitor = new SessionMonitor(new Adapter($session));

            $sessionMonitor->on('join', function (\stdClass $info) use ($sessionMonitor) {

                $this->monitoredSessionId = $info->session;
                $sessionMonitor->stop();
                $this->connection->close();

            });

            $sessionMonitor->on('start', function () use ($sessionMonitor) {

                // create a client session
                $clientConnection = $this->createConnection();

                $clientConnection->on('open', function (ClientSession $clientSession) use ($clientConnection) {
                    $this->clientSessionId = $clientSession->getSessionId();
                    $clientConnection->close();
                });
                $clientConnection->open();

            });

            $sessionMonitor->start();

        });

        $this->connection->on('error', function ($reason) {
            echo "The connected has closed with error: {$reason}\n";
        });

        $this->connection->open();

        $this->assertEquals($this->clientSessionId, $this->monitoredSessionId);

    }

    public function test_onleave()
    {


        $this->connection->on('open', function (ClientSession $session) {

            $sessionMonitor = new SessionMonitor(new Adapter($session));

            $sessionMonitor->on('leave', function ($id) use ($sessionMonitor) {

                $this->monitoredSessionId = $id;
                $sessionMonitor->stop();
                $this->connection->close();

            });

            $sessionMonitor->on('start', function () {

                // create a client session
                $clientConnection = $this->createConnection();

                $clientConnection->on('open', function (ClientSession $clientSession) use ($clientConnection) {
                    $this->clientSessionId = $clientSession->getSessionId();
                    $clientConnection->close();
                });
                $clientConnection->open();


            });

            $sessionMonitor->start();

        });

        $this->connection->on('error', function ($reason) {
            echo "The connected has closed with error: {$reason}\n";
        });

        $this->connection->open();

        $this->assertEquals($this->clientSessionId, $this->monitoredSessionId);

    }

}
