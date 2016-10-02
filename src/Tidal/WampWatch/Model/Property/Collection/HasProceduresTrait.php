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

namespace Tidal\WampWatch\Model\Property\Collection;

use Tidal\WampWatch\Model\Contract;
use Tidal\WampWatch\Model\Contract\Property\ObjectCollectionInterface;

/**
 * Class HasProceduresTrait
 *
 * Important! Classes using this trait have to also use trait
 * Tidal\WampWatch\Model\Behavior\Property\HasCollectionsTrait
 * for this trait to work;
 */
trait HasProceduresTrait
{

    protected static $proceduresPropertyName = 'procedures';

    /**
     * @var ObjectCollectionInterface
     */
    private $procedures;

    public function addProcedure(Contract\ProcedureInterface $procedure)
    {
        $this->getProcedures()->set($procedure->getUri(), $procedure);
    }

    /**
     * @return ObjectCollectionInterface
     */
    private function getProcedures()
    {
        return $this->getCollection(static::$proceduresPropertyName);
    }

    public function hasProcedure($uri)
    {
        return $this->getProcedures()->has($uri);
    }

    /**
     * @param string $uri
     *
     * @return Contract\ProcedureInterface
     */
    public function getProcedure($uri)
    {
        return $this->getProcedures()->get($uri);
    }

    /**
     * @param string $uri
     */
    public function removeProcedure($uri)
    {
        $this->getProcedures()->offsetUnset($uri);
    }

    /**
     * @return \Generator
     */
    public function listProcedures()
    {
        foreach ($this->getProcedures()->getIterator() as $uri => $procedure) {
            yield $uri => $procedure;
        }
    }

    /**
     * @param ObjectCollectionInterface $procedures
     */
    private function setProcedures(ObjectCollectionInterface $procedures)
    {
        $this->initCollection(static::$proceduresPropertyName, $procedures);
        $procedures->setObjectConstrain(Contract\ProcedureInterface::class);
    }
}