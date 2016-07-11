<?php

require_once __DIR__.'/../../bootstrap.php';

use \Tidal\WampWatch\MonitorTrait;
use Tidal\WampWatch\ClientSessionInterface as ClientSession;

class MonitorTraitImplementation
{
    use MonitorTrait;


    /**
     * Constructor.
     *
     * @param ClientSession $session
     */
    public function __construct(ClientSession $session)
    {
        $this->setClientSession($session);
    }
    
}
