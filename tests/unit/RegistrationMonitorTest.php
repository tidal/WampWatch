<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace tests\unit;

require_once __DIR__ . '/../bootstrap.php';

use Tidal\WampWatch\RegistrationMonitor;
use Tidal\WampWatch\Stub\ClientSessionStub;

/**
 * Class tests\unit\RegistrationMonitorTest *
 */
class RegistrationMonitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClientSessionStub
     */
    private $sessionStub;

    /**
     * @var RegistrationMonitor
     */
    private $monitor;

    public function setup()
    {
        $this->sessionStub = new ClientSessionStub();
        $this->monitor = new RegistrationMonitor($this->sessionStub);
    }

    public function test_can_create_instance()
    {
        $this->assertInstanceOf(
            RegistrationMonitor::class,
            $this->monitor
        );
    }

    public function test_starts_returns_true()
    {
        $res = $this->monitor->start();

        $this->assertEquals(true, $res);
    }
}
