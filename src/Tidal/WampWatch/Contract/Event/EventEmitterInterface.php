<?php
/**
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Contract\Event;

interface EventEmitterInterface
{
    /**
     * Registers a subscriber to be notified on given event.
     *
     * @param string   $eventName the event to subscribe to
     * @param callable $callback  the callback to notify the subscriber
     *
     * @return mixed
     */
    public function on($eventName, callable $callback);
}
