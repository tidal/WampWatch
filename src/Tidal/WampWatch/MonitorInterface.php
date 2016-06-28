<?php

namespace Tidal\WampWatch;

/*
 * Copyright 2016 Timo Michna.
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */


/**
 *
 * @author Timo Michna
 */
interface MonitorInterface {
    
    public function start();

    public function stop();
    
}
