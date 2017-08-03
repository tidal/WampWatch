<?php
/**
 *  * This file is part of the Tidal/WampWatch package.
 *  * (c) 2016 Timo Michna <timomichna/yahoo.de>
 *  *
 *  * For the full copyright and license information, please view the LICENSE
 *  * file that was distributed with this source code.
 */

namespace integration\crossbar;

use Psr\Log\NullLogger;
use Thruway\Logging\Logger;
use Thruway\Connection;
use React\EventLoop\Factory as LoopFactory;
use React\EventLoop\LoopInterface;
use Thruway\ClientSession;
use Tidal\WampWatch\Adapter\Thruway\ClientSession as Adapter;
use Tidal\WampWatch\Adapter\React\PromiseFactory;

trait CrossbarTestingTrait
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Adapter
     */
    private $clientSession;

    /**
     * @var Adapter
     */
    private $monitorSession;

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

    /**
     * @var string
     */
    private $testTopicName = 'foo';

    private function setupConnection()
    {
        $this->clientSessionId = -1;
        $this->monitoredSessionId = -2;

        $this->setupTestTopicName();
        $this->setupLogger();
        $this->createMonitorConnection();
    }

    /**
     * @param \React\EventLoop\LoopInterface|null $loop
     *
     * @return \Thruway\Connection
     */
    private function createConnection(LoopInterface $loop = null)
    {
        if ($loop === null) {
            $loop = $this->getLoop();
        }

        $connection = new Connection(
            [
                'realm' => self::REALM_NAME,
                'url'   => self::ROUTER_URL,
            ],
            $loop
        );

        $connection->on('error', function ($reason) {
            echo "The client connection has closed with error: {$reason}\n";
        });

        return $connection;
    }

    /**
     * Creates additional connections to mimick client behavior
     *
     * @return \Thruway\Connection
     */
    private function createMonitorConnection()
    {
        $connection = $this->getConnection();

        $connection->on('open', function (ClientSession $session) {
            $this->monitorSession = $this->createSessionAdapter($session);
            $this->monitoredSessionId = $session->getSessionId();
        });

        return $connection;
    }

    /**
     * Creates additional connections to mimick client behavior
     *
     * @return \Thruway\Connection
     */
    private function createClientConnection()
    {
        $connection = $this->createConnection();
        $connection->on('open', function (ClientSession $session) {
            $this->clientSession = $this->createSessionAdapter($session);
            $this->clientSessionId = $session->getSessionId();
        });
        // close the client connection when the monitor connection closes
        $this->getConnection()->on('close', [$connection, 'close']);

        return $connection;
    }

    private function setupTestTopicName()
    {
        $this->testTopicName = '';

        $chars = str_split('abcdefghijklmnopqrstuvwxyz');
        for ($i = 0; $i < 10; ++$i) {
            $key = array_rand($chars);
            $this->testTopicName .= '' . $chars[$key];
        }
    }

    /**
     * @return \React\EventLoop\LoopInterface
     */
    private function getLoop()
    {
        return $this->loop
            ?: $this->loop = LoopFactory::create();
    }

    /**
     * @param ClientSession $session
     *
     * @return Adapter
     */
    private function createSessionAdapter(ClientSession $session)
    {
        return new Adapter($session, new PromiseFactory());
    }

    /**
     * @return \Thruway\Connection
     */
    public function getConnection()
    {
        return $this->connection
            ?: $this->connection = $this->createConnection();
    }

    private function setupLogger()
    {
        Logger::set(new NullLogger());
    }
}
