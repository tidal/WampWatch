<?php

namespace integration\crossbar;

require_once realpath(__DIR__ . '/../..') . '/bootstrap.php';
require_once __DIR__ . '/CrossbarTestingTrait.php';

use Thruway\ClientSession;
use Thruway\Transport\TransportInterface;
use Tidal\WampWatch\SessionMonitor;
use stdClass;

class CrosssbarSessionMonitorTest extends \PHPUnit_Framework_TestCase
{
    use CrossbarTestingTrait;

    const REALM_NAME = 'realm1';

    const ROUTER_URL = 'ws://127.0.0.1:8080/ws';

    public function setup()
    {
        $this->setupConnection();
    }

    /**
     * test if the WAMP router supports session meta api
     */
    public function test_meta_api()
    {
        /** @var stdClass $connectionDetails */
        $connectionDetails = null;

        $onOpen = function (ClientSession $session, TransportInterface $transport, stdClass $details) use (&$connectionDetails) {

            $connectionDetails = $details;

            $this->connection->close();
        };

        $this->connection->on('open', $onOpen);

        $this->connection->on('error', function ($reason) {
            echo "The connected has closed with error: {$reason}\n";
        });

        $this->connection->open();

        // wait for connection to be established
        sleep(1);

        $this->assertInstanceOf(stdClass::class, $connectionDetails);
        $this->assertObjectHasAttribute('roles', $connectionDetails);

        $this->assertObjectHasAttribute('broker', $connectionDetails->roles);
        $this->assertObjectHasAttribute('features', $connectionDetails->roles->broker);
        $this->assertObjectHasAttribute('session_meta_api', $connectionDetails->roles->broker->features);
        $this->assertTrue($connectionDetails->roles->broker->features->session_meta_api);

        $this->assertObjectHasAttribute('dealer', $connectionDetails->roles);
        $this->assertObjectHasAttribute('features', $connectionDetails->roles->dealer);
        $this->assertObjectHasAttribute('session_meta_api', $connectionDetails->roles->dealer->features);
        $this->assertTrue($connectionDetails->roles->dealer->features->session_meta_api);
    }

    public function test_onstart()
    {
        $sessionIds = null;

        $this->connection->on('open', function (ClientSession $session) use (&$sessionIds) {
            $sessionMonitor = new SessionMonitor($this->createSessionAdapter($session));

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

        // wait for connection to be established
        sleep(1);

        $this->assertTrue(is_array($sessionIds));
    }

    public function test_onjoin()
    {
        $this->connection->on('open', function (ClientSession $session) {
            $sessionMonitor = new SessionMonitor($this->createSessionAdapter($session));

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

        // wait for connection to be established
        sleep(1);

        $this->assertEquals($this->clientSessionId, $this->monitoredSessionId);
    }

    public function test_onleave()
    {
        $this->connection->on('open', function (ClientSession $session) {
            $sessionMonitor = new SessionMonitor($this->createSessionAdapter($session));

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

        // wait for connection to be established
        sleep(1);

        $this->assertEquals($this->clientSessionId, $this->monitoredSessionId);
    }
}
