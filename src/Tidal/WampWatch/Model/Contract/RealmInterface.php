<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Model\Contract;

use Tidal\WampWatch\Model\Contract\Property\Scalar\HasNameInterface;
use Tidal\WampWatch\Model\Contract\Property\Collection\HasTopicsInterface;

interface RealmInterface extends HasNameInterface, HasTopicsInterface
{
    public function getProcedure($uri);
}
