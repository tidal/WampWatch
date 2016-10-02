<?php
/**
 *
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>
 *  *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace integration\crossbar;

require_once realpath(__DIR__ . "/../..") . "/bootstrap.php";
require_once __DIR__ . "/CrossbarTestingTrait.php";

use Thruway\ClientSession;
use Thruway\Message\SubscribedMessage;
use Tidal\WampWatch\SubscriptionMonitor;
use Tidal\WampWatch\Adapter\Thruway\ClientSession as Adapter;
use Tidal\WampWatch\Util;
use stdClass;

class CrosssbarSubscriptionMonitorTest extends \PHPUnit_Framework_TestCase
{

    use CrossbarTestingTrait;

    const REALM_NAME = 'realm1';

    const ROUTER_URL = 'ws://127.0.0.1:8080/ws';

    /**
     * @var SubscriptionMonitor
     */
    private $subscriptionMonitor;

    /**
     * @var stdClass
     */
    private $initialSubscriptionInfo;

    /**
     * @var int
     */
    private $creatorSessionId;

    /**
     * @var stdClass
     */
    private $clientSubscriptionInfo;

    /**
     * @var int
     */
    private $subscriberSessionId;

    /**
     * @var int
     */
    private $subscriberSubId;

    public function setup()
    {
        $this->setupConnection();

        $this->getConnection()->on('open', function (ClientSession $session) {
            $this->setupSubscriptionMonitor($session);
        });
    }

    public function test_onstart()
    {
        $this->getConnection()->on('open', function () {

            $this->subscriptionMonitor->on('start', [$this->subscriptionMonitor, 'stop']);

            $this->subscriptionMonitor->start();
        });

        $this->connection->open();

        $this->validateSubscriptionInfo($this->initialSubscriptionInfo);
    }

    public function test_onstart_delivers_current_list()
    {
        $subscriptionId = null;

        // create an additional client session
        $clientConnection = $this->createClientConnection();
        $clientConnection->on('open', function () use (&$subscriptionId, $clientConnection) {

            $this->clientSession->subscribe($this->testTopicName, function () {
            })
                ->done(function (SubscribedMessage $message) use (&$subscriptionId) {
                    $subscriptionId = $message->getSubscriptionId();

                    $this->getConnection()->open();
                });

        });

        $this->getConnection()->on('open', function () use ($clientConnection) {
            $this->subscriptionMonitor->on('start', [$this->subscriptionMonitor, 'stop']);
            $this->subscriptionMonitor->start();
        });

        $clientConnection->open();

        $this->validateSubscriptionInfo($this->initialSubscriptionInfo);
        $this->assertArraySubset([$subscriptionId], $this->initialSubscriptionInfo->exact);
    }

    public function test_oncreate()
    {
        $this->getConnection()->on('open', function () {

            // create an additional client session
            $clientConnection = $this->createClientConnection();
            $clientConnection->on('open', function () use ($clientConnection) {
                $this->clientSession->subscribe($this->testTopicName, function () {
                });
            });

            $this->subscriptionMonitor->on('start', function () use ($clientConnection) {
                $clientConnection->open();
            });

            $this->subscriptionMonitor->on('create', function ($sessionId) {
                $this->assertEquals($this->clientSession->getSessionId(), $sessionId);

                $this->subscriptionMonitor->stop();
            });

            $this->subscriptionMonitor->start();
        });

        $this->getConnection()->open();

        $this->assertInternalType('int', $this->creatorSessionId);
        $this->assertInstanceOf(\stdClass::class, $this->clientSubscriptionInfo);
        $this->assertAttributeEquals($this->testTopicName, 'uri', $this->clientSubscriptionInfo);
        $this->assertAttributeEquals('exact', 'match', $this->clientSubscriptionInfo);
        $this->assertAttributeInternalType('int', 'id', $this->clientSubscriptionInfo);
        $this->assertAttributeInternalType('string', 'created', $this->clientSubscriptionInfo);
    }

    public function test_onsubscribe()
    {
        $this->connection->on('open', function () {

            $this->subscriptionMonitor->on('subscribe', function () {
                $this->subscriptionMonitor->stop();
            });

            $this->subscriptionMonitor->on('start', function () {

                // create an additional client session
                $clientConnection = $this->createClientConnection();

                $clientConnection->on('open', function () use ($clientConnection) {
                    $this->clientSession->subscribe($this->testTopicName, function () {
                    });
                });
                $clientConnection->open();
            });

            $this->subscriptionMonitor->start();

        });

        $this->connection->open();

        $this->assertEquals($this->creatorSessionId, $this->subscriberSessionId);
        $this->assertEquals($this->clientSubscriptionInfo->id, $this->subscriberSubId);
    }

    public function test_onunsubscribe()
    {

        $sessionId = null;
        $subscriptionId = null;

        $this->connection->on('open', function () use (&$sessionId, &$subscriptionId) {

            // create an additional client session
            $clientConnection = $this->createConnection($this->loop);
            $clientSession = null;

            $this->subscriptionMonitor->on('start', function () use ($clientConnection) {
                $clientConnection->open();

                $this->subscriptionMonitor->stop();
                $this->connection->close();
            });

            $clientConnection->on('open', function (ClientSession $clientSession) use ($clientConnection, &$subscriptionMonitor) {
                $this->subscriptionMonitor->on('subscribe', function () use ($clientSession, $clientConnection) {
                    Util::unsubscribe($this->createSessionAdapter($clientSession), func_get_args()[1]);
                    $clientConnection->close();
                });

                $clientSession->subscribe('foo', function () {
                });
            });

            $this->subscriptionMonitor->on('unsubscribe', function ($sesId, $subId) use (&$sessionId, &$subscriptionId, $clientConnection) {

                $sessionId = $sesId;
                $subscriptionId = $subId;

                $this->subscriptionMonitor->stop();
            });

            $this->subscriptionMonitor->start();

        });

        $this->connection->open();

        $this->assertInternalType('int', $sessionId);
        $this->assertInternalType('int', $subscriptionId);
    }

    public function test_ondelete()
    {
        $sessionId = null;
        $subscriptionId = null;

        $this->connection->on('open', function () use (&$sessionId, &$subscriptionId) {

            // create an additional client session
            $clientConnection = $this->createConnection($this->loop);
            $clientSession = null;

            $this->subscriptionMonitor->on('stop', function () {
                $this->connection->close();
            });

            $this->subscriptionMonitor->on('delete', function ($sesId, $subId) use (&$sessionId, &$subscriptionId, $clientConnection) {
                $sessionId = $sesId;
                $subscriptionId = $subId;

                $clientConnection->close();
                $this->subscriptionMonitor->stop();
                $this->connection->close();
            });

            $clientConnection->on('open', function (ClientSession $clientSession) use ($clientConnection, &$subscriptionMonitor) {
                $adapter = $this->createSessionAdapter($clientSession);
                $this->clientSessionId = $adapter->getSessionId();

                $this->subscriptionMonitor->on('subscribe', function () use (&$sessionId, &$subscriptionId, $subscriptionMonitor, $clientConnection, $adapter) {
                    Util::unsubscribe($adapter, func_get_args()[1]);
                });

                $adapter->subscribe($this->testTopicName, function () {
                });
            });

            $this->subscriptionMonitor->on('start', function () use ($clientConnection, $clientConnection) {
                $clientConnection->open();
            });

            $this->subscriptionMonitor->start();

        });

        $this->connection->open();

        $this->assertInternalType('int', $sessionId);
        $this->assertInternalType('int', $subscriptionId);
    }

    private function setupSubscriptionMonitor(ClientSession $session)
    {
        $this->subscriptionMonitor = new SubscriptionMonitor(new Adapter($session));
        $this->subscriptionMonitor->on('stop', function () {
            $this->connection->close();
        });
        $this->subscriptionMonitor->on('error', function ($error) {
            var_dump($error);

            $this->connection->close();

            die();
        });
        $this->subscriptionMonitor->on('start', function ($info) {
            $this->initialSubscriptionInfo = $info;
        });

        $this->subscriptionMonitor->on('create', function ($sessionId, \stdClass $subInfo) {
            $this->creatorSessionId = $sessionId;
            $this->clientSubscriptionInfo = $subInfo;
        });

        $this->subscriptionMonitor->on('subscribe', function ($sessionId, $subId) {
            $this->subscriberSessionId = $sessionId;
            $this->subscriberSubId = $subId;
        });
    }

    private function validateSubscriptionInfo(stdClass $subscriptionInfo)
    {
        $this->assertAttributeInternalType('array', 'exact', $subscriptionInfo);
        $this->assertAttributeInternalType('array', 'prefix', $subscriptionInfo);
        $this->assertAttributeInternalType('array', 'wildcard', $subscriptionInfo);
    }

}
