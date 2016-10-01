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
use Tidal\WampWatch\SubscriptionMonitor;
use Tidal\WampWatch\Adapter\Thruway\ClientSession as Adapter;
use Tidal\WampWatch\Util;

class CrosssbarSubscriptionMonitorTest extends \PHPUnit_Framework_TestCase
{

    use CrossbarTestingTrait;

    const REALM_NAME = 'realm1';

    const ROUTER_URL = 'ws://127.0.0.1:8080/ws';

    public function setup()
    {
        $this->setupConnection();
    }

    public function test_onstart()
    {

        $subscriptionInfo = null;

        $this->connection->on('open', function (ClientSession $session) use (&$subscriptionInfo) {

            $subscriptionMonitor = new SubscriptionMonitor(new Adapter($session));

            $subscriptionMonitor->on('start', function ($ids) use (&$subscriptionInfo, $subscriptionMonitor) {

                $subscriptionInfo = $ids;

                $subscriptionMonitor->stop();
                $this->connection->close();
            });

            $subscriptionMonitor->on('error', function ($error) use (&$subscriptionInfo, $subscriptionMonitor) {

                var_dump($error);

                $this->connection->close();

                die();
            });

            $subscriptionMonitor->start();
        });

        $this->connection->on('error', function ($reason) {
            echo "The connected has closed with error: {$reason}\n";
        });

        $this->connection->open();

        $this->assertInstanceOf(\stdClass::class, $subscriptionInfo);
        $this->assertAttributeInternalType('array', 'exact', $subscriptionInfo);
        $this->assertAttributeInternalType('array', 'prefix', $subscriptionInfo);
        $this->assertAttributeInternalType('array', 'wildcard', $subscriptionInfo);
    }

    public function test_oncreate()
    {
        $info = new \stdClass();
        $info->session = null;
        $info->subscription = null;

        $subscriptionSessionId = null;
        $subscriptionInfo = null;

        $this->connection->on('open', function (ClientSession $session) use ($info) {

            $subscriptionMonitor = new SubscriptionMonitor(new Adapter($session));

            $subscriptionMonitor->on('start', function () use ($subscriptionMonitor) {

                // create an additional client session
                $clientConnection = $this->createConnection();

                $clientConnection->on('open', function (ClientSession $clientSession) use ($clientConnection) {
                    $this->clientSessionId = $clientSession->getSessionId();
                    $clientSession->subscribe('foo', function () {
                    });
                    $clientConnection->close();
                });
                $clientConnection->open();

                $subscriptionMonitor->stop();
                $this->connection->close();
            });

            $subscriptionMonitor->on('create', function ($sessionId, \stdClass $subInfo) use ($info, $subscriptionMonitor) {

                $info->subscription = $subInfo;
                $info->session = $sessionId;

                $subscriptionMonitor->stop();
                $this->connection->close();
            });

            $subscriptionMonitor->on('error', function ($error) use (&$subscriptionInfo, $subscriptionMonitor) {

                var_dump($error);

                $this->connection->close();
            });

            $subscriptionMonitor->start();

        });

        $this->connection->on('error', function ($reason) {
            echo "The connected has closed with error: {$reason}\n";
        });

        $this->connection->open();

        //$this->assertInternalType('int', $subscriptionSessionId);
        $this->assertInstanceOf(\stdClass::class, $info->subscription);
        $this->assertAttributeEquals('foo', 'uri', $info->subscription);
        $this->assertAttributeEquals('exact', 'match', $info->subscription);
        $this->assertAttributeInternalType('int', 'id', $info->subscription);
        $this->assertAttributeInternalType('string', 'created', $info->subscription);
    }

    public function test_onsubscribe()
    {
        $sessionId = null;
        $subscriptionId = null;

        $this->connection->on('open', function (ClientSession $session) use (&$sessionId, &$subscriptionId) {

            $subscriptionMonitor = new SubscriptionMonitor(new Adapter($session));

            $subscriptionMonitor->on('start', function () use ($subscriptionMonitor) {

                // create an additional client session
                $clientConnection = $this->createConnection();

                $clientConnection->on('open', function (ClientSession $clientSession) use ($clientConnection) {
                    $this->clientSessionId = $clientSession->getSessionId();
                    $clientSession->subscribe('foo', function () {
                    });
                    $clientConnection->close();
                });
                $clientConnection->open();

                $subscriptionMonitor->stop();
                $this->connection->close();
            });

            $subscriptionMonitor->on('subscribe', function ($sesId, $subId) use (&$sessionId, &$subscriptionId, $subscriptionMonitor) {

                $sessionId = $sesId;
                $subscriptionId = $subId;

                $subscriptionMonitor->stop();
                $this->connection->close();
            });

            $subscriptionMonitor->on('error', function ($error) use ($subscriptionMonitor) {

                var_dump($error);
                $subscriptionMonitor->stop();
                $this->connection->close();
            });

            $subscriptionMonitor->start();

        });

        $this->connection->on('error', function ($reason) {
            echo "The connected has closed with error: {$reason}\n";
        });

        $this->connection->open();

        $this->assertInternalType('int', $sessionId);
        $this->assertInternalType('int', $subscriptionId);
    }

    public function test_onunsubscribe()
    {

        $sessionId = null;
        $subscriptionId = null;

        $this->connection->on('open', function (ClientSession $session) use (&$sessionId, &$subscriptionId) {

            $subscriptionMonitor = new SubscriptionMonitor(new Adapter($session));

            // create an additional client session
            $clientConnection = $this->createConnection();
            $clientSession = null;

            $subscriptionMonitor->on('start', function () use ($subscriptionMonitor, $clientConnection, $clientConnection) {
                $clientConnection->open();

                $subscriptionMonitor->stop();
                $this->connection->close();
            });

            $clientConnection->on('open', function (ClientSession $clientSession) use ($clientConnection, &$subscriptionMonitor) {

                $this->clientSessionId = $clientSession->getSessionId();

                $subscriptionMonitor->on('subscribe', function () use ($clientSession) {
                    Util::unsubscribe(new Adapter($clientSession), func_get_args()[1]);
                });

                $clientSession->subscribe('foo', function () {
                });

                $clientConnection->close();

            });

            $subscriptionMonitor->on('unsubscribe', function ($sesId, $subId) use (&$sessionId, &$subscriptionId, $subscriptionMonitor, $clientConnection) {

                $sessionId = $sesId;
                $subscriptionId = $subId;

                $clientConnection->close();
                $subscriptionMonitor->stop();
                $this->connection->close();
            });

            $subscriptionMonitor->on('error', function ($error) use ($subscriptionMonitor) {

                var_dump($error);
                $subscriptionMonitor->stop();
                $this->connection->close();
            });

            $subscriptionMonitor->start();

        });

        $this->connection->on('error', function ($reason) {
            echo "The connected has closed with error: {$reason}\n";
        });

        $this->connection->open();

        $this->assertInternalType('int', $sessionId);
        $this->assertInternalType('int', $subscriptionId);
    }

    public function test_ondelete()
    {
        $sessionId = null;
        $subscriptionId = null;

        $this->connection->on('open', function (ClientSession $session) use (&$sessionId, &$subscriptionId) {

            $subscriptionMonitor = new SubscriptionMonitor(new Adapter($session));

            // create an additional client session
            $clientConnection = $this->createConnection();
            $clientSession = null;

            $subscriptionMonitor->on('start', function () use ($subscriptionMonitor, $clientConnection, $clientConnection) {
                $clientConnection->open();

                $subscriptionMonitor->stop();
                $this->connection->close();
            });

            $clientConnection->on('open', function (ClientSession $clientSession) use ($clientConnection, &$subscriptionMonitor) {

                $topicName = "foo-bar-baz-boo";
                $this->clientSessionId = $clientSession->getSessionId();

                $subscriptionMonitor->on('subscribe', function () use ($clientSession) {
                    Util::unsubscribe(new Adapter($clientSession), func_get_args()[1]);
                });

                $clientSession->subscribe($topicName, function () {
                });

                $clientConnection->close();

            });

            $subscriptionMonitor->on('delete', function ($sesId, $subId) use (&$sessionId, &$subscriptionId, $subscriptionMonitor, $clientConnection) {

                $sessionId = $sesId;
                $subscriptionId = $subId;

                $clientConnection->close();
                $subscriptionMonitor->stop();
                $this->connection->close();
            });

            $subscriptionMonitor->on('error', function ($error) use ($subscriptionMonitor) {

                var_dump($error);
                $subscriptionMonitor->stop();
                $this->connection->close();
            });

            $subscriptionMonitor->start();

        });

        $this->connection->on('error', function ($reason) {
            echo "The connected has closed with error: {$reason}\n";
        });

        $this->connection->open();

        $this->assertInternalType('int', $sessionId);
        $this->assertInternalType('int', $subscriptionId);
    }

}
