<?php
/**
 * Created by PhpStorm.
 * User: Timo
 * Date: 24.07.2016
 * Time: 17:11.
 */

namespace Tidal\WampWatch\Model;

use Tidal\WampWatch\Model\Property\Scalar\HasNameTrait;
use Tidal\WampWatch\Model\Property\Collection\HasTopicsTrait;
use Tidal\WampWatch\Model\Property\Collection\HasProceduresTrait;
use Tidal\WampWatch\Model\Property\Object\HasRouterTrait;
use Tidal\WampWatch\Model\Behavior\Property\HasCollectionsTrait;
use Tidal\WampWatch\Model\Property\ObjectCollection;

class Realm implements Contract\RealmInterface
{
    use HasCollectionsTrait;
    use HasNameTrait;
    use HasTopicsTrait;
    use HasProceduresTrait;
    use HasRouterTrait;

    public function __construct($name, Contract\RouterInterface $router)
    {
        $this->setName($name);
        $this->setRouter($router);
        $this->setProcedures(new ObjectCollection());
        $this->setTopics(new ObjectCollection());
    }
}
