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

use Tidal\WampWatch\Model\Contract\RegistrationInterface;
use Tidal\WampWatch\Model\Contract\SessionInterface;
use Tidal\WampWatch\Model\Contract\ProcedureInterface;
use Tidal\WampWatch\Model\Property\Scalar\HasIdTrait;
use Tidal\WampWatch\Model\Property\Object\HasSessionTrait;
use Tidal\WampWatch\Model\Property\Object\HasProcedureTrait;

class Registration implements RegistrationInterface
{
    use HasIdTrait;
    use HasSessionTrait;
    use HasProcedureTrait;

    public function __construct($id, SessionInterface $session, ProcedureInterface $procedure)
    {
        $this->setId($id);
        $this->setSession($session);
        $this->setProcedure($procedure);
    }
}
