<?php

require_once __DIR__ . '/../../bootstrap.php';

use \Tidal\WampWatch\MonitorTrait;
use Tidal\WampWatch\ClientSessionInterface as ClientSession;

class MonitorTraitImplementation
{
    use MonitorTrait {
        setInitialCall as doSetInitialCall;
    }

    /**
     * Constructor.
     *
     * @param ClientSession $session
     */
    public function __construct(ClientSession $session)
    {
        $this->setClientSession($session);
    }

    /**
     * @param string   $procedure
     * @param callable $callback
     */
    public function setInitialCall($procedure, callable $callback)
    {
        $this->doSetInitialCall($procedure, $callback);
    }
}
