<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Model\Property\Object;

use Tidal\WampWatch\Model\Contract;

trait HasRouterTrait
{
    /**
     * @var Contract\RouterInterface
     */
    private $router;

    /**
     * @return Contract\RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param Contract\RouterInterface $router
     */
    private function setRouter(Contract\RouterInterface $router)
    {
        $this->router = $router;
    }
}
