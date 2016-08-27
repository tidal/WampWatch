<?php
/**
 *
 *  * This file is part of the Tidal/WampWatch package.
 *  * (c) 2016 Timo Michna <timomichna/yahoo.de>
 *  *
 *  * For the full copyright and license information, please view the LICENSE
 *  * file that was distributed with this source code.
 *
 */


namespace Tidal\WampWatch\Model\Property\Object;

use Tidal\WampWatch\Model\Contract;

trait HasSessionTrait
{
    /**
     * @var Contract\SessionInterface
     */
    private $session;

    /**
     * @param Contract\SessionInterface $session
     */
    private function setSession(Contract\SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @return Contract\SessionInterface
     */
    public function getSession()
    {
        return $this->session;
    }


}