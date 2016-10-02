<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
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
