<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

/**
 * Created by PhpStorm.
 * User: Timo
 * Date: 25.07.2016
 * Time: 20:07.
 */

namespace Tidal\WampWatch\Model\Property\Scalar;

trait HasUriTrait
{
    private $uri;

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    private function setUri($uri)
    {
        $this->uri = (string) $uri;
    }
}
