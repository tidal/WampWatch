<?php

namespace Tidal\WampWatch;

/*
 * Copyright 2016 Timo Michna.
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

use Evenement\EventEmitterTrait;
use Thruway\ClientSession;

/**
 * Description of MonitorTrait.
 *
 * @author Timo
 */
trait MonitorTrait
{
    use EventEmitterTrait;

    /**
     * The monitor's WAMP client session.
     *
     * @var \Thruway\ClientSession
     */
    protected $session;

    /**
     * Wether the monitor is running.
     *
     * @var bool
     */
    protected $isRunning = false;
    

    /**
     * @param \Thruway\ClientSession $session
     */
    protected function setClientSession(ClientSession $session)
    {
        $this->session = $session;
    }


    /**
     * Start the monitor.
     *
     * @return bool
     */
    public function start()
    {
        $this->isRunning = true;
        $this->emit('start', [$this->getList()]);

        return true;
    }

    /**
     * Stop the monitor.
     * Returns boolean wether the monitor could be started.
     *
     * @return bool
     */
    public function stop()
    {
        if (!$this->isRunning()) {
            return false;
        }
        $this->isRunning = false;
        $this->emit('stop', [$this]);

        return true;
    }

    protected function getList()
    {
        return [];
    }

    /**
     * Get the monitor's WAMP client session.
     *
     * @return \Thruway\ClientSession
     */
    public function getServerSession()
    {
        return $this->session;
    }

    /**
     * Get the monitor's running state.
     *
     * @return bool
     */
    public function isRunning()
    {
        return $this->isRunning;
    }
}
