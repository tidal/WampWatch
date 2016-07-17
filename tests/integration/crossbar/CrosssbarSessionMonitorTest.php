<?php

namespace integration\crossbar;

require_once realpath(__DIR__ . "/../..") . "/bootstrap.php";


use Psr\Log\NullLogger;
use Thruway\Logging\Logger;
use Thruway\ClientSession;
use Thruway\Connection;
use React\EventLoop\Factory as LoopFactory;
use React\EventLoop\LoopInterface;
use Tidal\WampWatch\SessionMonitor;
use Tidal\WampWatch\Adapter\Thruway\ClientSession as Adapter;

class CrosssbarSessionMonitorTest extends \PHPUnit_Framework_TestCase
{

    const REALM_NAME = 'realm1';

    const ROUTER_URL = 'ws://127.0.0.1:8080/ws';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var int
     */
    private $clientSessionId = -1;

    /**
     * @var int
     */
    private $monitoredSessionId = -2;

    public function setup()
    {

        $this->clientSessionId = -1;
        $this->monitoredSessionId = -2;

        Logger::set(new NullLogger());

        $this->loop = LoopFactory::create();

        $this->connection = $this->createConnection($this->loop);

    }


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

    private function createConnection(LoopInterface $loop = null)
    {
        if ($loop = null) {
            $loop = LoopFactory::create();
        }

        return new Connection(
            [
                'realm' => self::REALM_NAME,
                'url'   => self::ROUTER_URL,
            ],
            $loop
        );
    }

}
