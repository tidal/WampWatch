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

use React\Promise\Deferred;
use Thruway\Message\UnsubscribeMessage;
use Thruway\Common\Utils;

/**
 * Utility Class for the WampWatch package.
 *
 * @author Timo Michna
 */
class Util
{
    public static function unsubscribe(ClientSessionInterface $session, $subscriptionId)
    {
        $requestId = Utils::getUniqueId();
        $deferred = new Deferred();
        $unsubscribeMsg = new UnsubscribeMessage($requestId, $subscriptionId);
        $session->sendMessage($unsubscribeMsg);

        return $deferred->promise();
    }
}
