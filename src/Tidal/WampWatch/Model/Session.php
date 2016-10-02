<?php

/**
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Model;

use Tidal\WampWatch\Model\Property\Scalar\HasIdTrait;

class Session implements Contract\SessionInterface
{
    use HasIdTrait;

    public function __construct($id)
    {
        $this->setId($id);
    }
}
