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

namespace Tidal\WampWatch\Model\EventSourcing\Action;


interface ActionInterface
{
    /**
     * @return string
     */
    public function __toString();

    /**
     * @return mstring fully qualified class name
     */
    public function getScope();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @return float
     */
    public function getTime();
}