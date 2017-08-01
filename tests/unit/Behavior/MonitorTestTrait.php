<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\Unit\Behavior;

use Tidal\WampWatch\Stub\ClientSessionStub;

/**
 * Trait tests\unit\Behavior\MonitorTestTrait *
 */
trait MonitorTestTrait
{
    /**
     * @var ClientSessionStub
     */
    private $sessionStub;

    private function setUpSessionStub()
    {
        $this->sessionStub = new ClientSessionStub();
    }

    private function getSubscriptionIdMap()
    {
        return json_decode('{"exact": [321], "prefix": [654], "wildcard": [987]}');
    }

}