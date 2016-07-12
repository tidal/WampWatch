<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Exception;

class UnknownProcedureException extends \OutOfBoundsException
{
    /**
     * @var string name of the procedure
     */
    protected $procedureName;

    /**
     * @param string $procedureName
     */
    public function __construct($procedureName)
    {
        $this->setprocedureName($procedureName);

        parent::__construct("unknown procedure '$procedureName'");
    }

    /**
     * @param string $procedureName
     */
    protected function setProcedureName($procedureName)
    {
        $this->procedureName = $procedureName;
    }

    /**
     * @return string the procedure name
     */
    public function getProcedureName()
    {
        return $this->procedureName;
    }
}
