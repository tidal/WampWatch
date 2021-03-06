<?php
/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Test\Unit\Stub;

use Tidal\WampWatch\MonitorTrait;
use Tidal\WampWatch\ClientSessionInterface as ClientSession;

class MonitorTraitImplementation
{
    use MonitorTrait {
        setInitialCall as doSetInitialCall;
    }

    /**
     * @var \stdClass
     */
    private $list;

    /**
     * Constructor.
     *
     * @param ClientSession $session
     */
    public function __construct(ClientSession $session)
    {
        $this->setClientSession($session);
    }

    public function setInitialCall($procedure, callable $callback)
    {
        $this->doSetInitialCall($procedure, $callback);
    }

    protected function setList($list)
    {
        $this->list = $list;
    }
}
