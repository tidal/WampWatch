<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */


namespace Tidal\WampWatch;

/**
 * @author Timo Michna
 */
interface MonitorInterface
{
    public function start();

    public function stop();
}
