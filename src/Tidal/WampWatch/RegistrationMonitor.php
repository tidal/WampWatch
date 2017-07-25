<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch;

use Tidal\WampWatch\ClientSessionInterface as ClientSession;

/**
 * Class Tidal\WampWatch\RegistrationMonitor *
 */
class RegistrationMonitor
{
    use MonitorTrait;

    const REGISTRATION_CREATE_TOPIC = 'wamp.registration.on_create';
    const REGISTRATION_REG_TOPIC = 'wamp.registration.on_register';
    const REGISTRATION_UNREG_TOPIC = 'wamp.registration.on_unregister';
    const REGISTRATION_DELETE_TOPIC = 'wamp.registration.on_delete';
    const REGISTRATION_LIST_TOPIC = 'wamp.registration.list';
    const REGISTRATION_LOOKUP_TOPIC = 'wamp.registration.lookup';
    const REGISTRATION_MATCH_TOPIC = 'wamp.registration.match';
    const REGISTRATION_GET_TOPIC = 'wamp.registration.get';
    const REGISTRATION_REGLIST_TOPIC = 'wamp.registration.list_callees';
    const REGISTRATION_REGCOUNT_TOPIC = 'wamp.registration.count_callees';

    const LOOKUP_MATCH_WILDCARD = MonitorInterface::LOOKUP_MATCH_WILDCARD;
    const LOOKUP_MATCH_PREFIX = MonitorInterface::LOOKUP_MATCH_PREFIX;


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