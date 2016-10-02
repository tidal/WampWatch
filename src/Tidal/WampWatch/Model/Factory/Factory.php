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

/**
 * Created by PhpStorm.
 * User: Timo
 * Date: 24.07.2016
 * Time: 18:50
 */

namespace Tidal\WampWatch\Model\Factory;

use Tidal\WampWatch\Model;

class Factory
{


    public function createRouter($uri, array $realms = [])
    {
        return new Model\Router($uri);
    }

}