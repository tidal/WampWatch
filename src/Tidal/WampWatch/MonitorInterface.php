<?php

namespace Tidal\WampWatch;



/**
 * @author Timo Michna
 */
interface MonitorInterface
{
    public function start();

    public function stop();
}
