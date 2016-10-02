<?php
/**
 *
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace Tidal\WampWatch\Model\Property\Object;


use Tidal\WampWatch\Model\Contract;

trait HasRealmTrait
{
    /**
     * @var Contract\RealmInterface
     */
    private $realm;

    /**
     * @return Contract\RealmInterface
     */
    public function getRealm()
    {
        return $this->realm;
    }

    /**
     * @param Contract\RealmInterface $realm
     */
    private function setRealm(Contract\RealmInterface $realm)
    {
        $this->realm = $realm;
    }


}