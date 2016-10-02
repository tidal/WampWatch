<?php
/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Model\Property\Collection;

use Tidal\WampWatch\Model\Contract;
use Tidal\WampWatch\Model\Behavior\Property\HasCollectionsTrait;
use Tidal\WampWatch\Model\Contract\Property\ObjectCollectionInterface;
use Tidal\WampWatch\Model\Contract\RealmInterface;

/**
 * Class HasRealmsTrait
 *
 * Important! Classes using this trait have to also use trait
 * Tidal\WampWatch\Model\Behavior\Property\HasCollectionsTrait
 * for this trait to work;
 */
trait HasRealmsTrait
{

    //use HasCollectionsTrait;

    protected $realmsPropertyName = 'realms';

    /**
     * @var ObjectCollectionInterface
     */
    private $realms;

    public function addRealm(Contract\RealmInterface $realm)
    {
        $this->getRealms()->set($realm->getName(), $realm);
    }

    /**
     * @return ObjectCollectionInterface
     */
    private function getRealms()
    {
        return $this->getCollection($this->realmsPropertyName);
    }

    public function hasRealm($name)
    {
        return $this->getRealms()->has($name);
    }

    /**
     * @param $name
     *
     * @return Contract\RealmInterface
     */
    public function getRealm($name)
    {
        return $this->getRealms()->get($name);
    }

    /**
     * @return \Generator
     */
    public function listRealms()
    {
        foreach ($this->getRealms()->getIterator() as $name => $realm){
            yield $name => $realm;
        }
    }

    /**
     * @param ObjectCollectionInterface $realms
     * @return \ArrayObject
     */
    private function setRealms(ObjectCollectionInterface $realms)
    {
        $this->initCollection($this->realmsPropertyName, $realms);
        $realms->setObjectConstrain(RealmInterface::class);
    }


}