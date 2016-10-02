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

use Tidal\WampWatch\Model\Property\Scalar\HasUriTrait;

class Procedure implements Contract\ProcedureInterface
{
    use HasUriTrait;

    public function __construct($uri)
    {
        $this->setUri($uri);
    }
}
