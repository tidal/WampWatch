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


interface DomainActionInterface extends ActionInterface
{
    /**
     * @param string $name
     *
     * @return DomainActionInterface
     */
    public static function create($name = '');

    /**
     * @param $scope
     *
     * @return DomainActionInterface
     */
    public function bind($scope);

    /**
     * @param string $name
     * @return DomainActionInterface
     */
    public function name($name);


    /**
     * @param callable $callback
     */
    public function subscribe(callable $callback);

    /**
     * @param callable $callback
     */
    public function unsubscribe(callable $callback);

    /**
     * @param string $aliasName
     *
     * @return DomainActionInterface
     */
    public function alias($aliasName);

    /**
     * @return string
     */
    public function getAlias();

    /**
     * @param object|string $object
     *
     * @return bool
     */
    public function isBoundTo($object);
}