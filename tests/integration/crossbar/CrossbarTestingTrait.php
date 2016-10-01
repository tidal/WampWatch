<?php
/**
 *
 *  * This file is part of the Tidal/WampWatch package.
 *  * (c) 2016 Timo Michna <timomichna/yahoo.de>
 *  *
 *  * For the full copyright and license information, please view the LICENSE
 *  * file that was distributed with this source code.
 *
 */

namespace integration\crossbar;

use Psr\Log\NullLogger;
use Thruway\Logging\Logger;
use Thruway\Connection;
use React\EventLoop\Factory as LoopFactory;
use React\EventLoop\LoopInterface;

trait CrossbarTestingTrait
{
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

    private function setupConnection()
    {

        $this->clientSessionId = -1;
        $this->monitoredSessionId = -2;

        Logger::set(new NullLogger());

        $this->loop = LoopFactory::create();

        $this->connection = $this->createConnection($this->loop);

    }

    /**
     * @param \React\EventLoop\LoopInterface|null $loop
     *
     * @return \Thruway\Connection
     */
    private function createConnection(LoopInterface $loop = null)
    {
        if ($loop === null) {
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