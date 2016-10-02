<?php

namespace Tidal\WampWatch\Model\Contract;

use Tidal\WampWatch\Model\Contract;

interface RouterInterface
{
    /**
     * @return string;
     */
    public function getUri();

    /**
     * @param RealmInterface $realm
     */
    public function addRealm(Contract\RealmInterface $realm);

    /**
     * @return \Generator
     */
    public function listRealms();
}
