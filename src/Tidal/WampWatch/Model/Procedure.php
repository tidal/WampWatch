<?php
/**
 * Created by PhpStorm.
 * User: Timo
 * Date: 24.07.2016
 * Time: 17:10.
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
