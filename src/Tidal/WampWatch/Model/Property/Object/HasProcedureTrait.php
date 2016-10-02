<?php
/**
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Model\Property\Object;

use Tidal\WampWatch\Model\Contract;

trait HasProcedureTrait
{
    /**
     * @var Contract\ProcedureInterface
     */
    private $procedure;

    /**
     * @return Contract\ProcedureInterface
     */
    public function getProcedure()
    {
        return $this->procedure;
    }

    /**
     * @param Contract\ProcedureInterface $procedure
     */
    private function setProcedure(Contract\ProcedureInterface $procedure)
    {
        $this->procedure = $procedure;
    }
}
