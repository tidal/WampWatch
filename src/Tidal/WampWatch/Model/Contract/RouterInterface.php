<?php


namespace Tidal\WampWatch\Model\Contract;

use Psr\Http\Message\UriInterface;
use Tidal\WampWatch\Model\Contract;

interface RouterInterface
{

    /**
     * @return UriInterface;
     */
    public function getUri();

    /**
     * @param  RealmInterface $realm
     */
    public function addRealm(Contract\RealmInterface $realm);

    /**
     * @return \Generator
     */
    public function listRealms();

}