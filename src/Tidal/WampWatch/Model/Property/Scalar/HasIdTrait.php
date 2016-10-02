<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Model\Property\Scalar;

trait HasIdTrait
{
    private $id;

    public function getId()
    {
        return $this->id;
    }

    private function setId($id)
    {
        $this->id = $id;
    }
}
