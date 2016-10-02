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

use RuntimeException;
use Tidal\WampWatch\Model\EventSourcing\ValueObject\ValueObjectInterface;
use Tidal\WampWatch\Model\EventSourcing\ValueObject\ValueObjectTrait;


trait DomainActionTrait
{
    use ValueObjectTrait;

    /**
     * @var string
     */
    public $scope;

    /**
     * @var string
     */
    public $name;

    /**
     * @var ValueObjectInterface
     */
    public $data;

    /**
     * @var float
     */
    public $time;

    /**
     * @var string
     */
    public $aliasName;

    private $subscribers = [];

    abstract protected function __construct($scope = null, $name = '', ValueObjectInterface $data = null);

    abstract public static function create($name = '');

    public function bind($scope)
    {
        if (trim((string)$this->scope) !== '') {
            throw new RuntimeException("Scope has already been set.");
        }

        return $this->export(new self($scope, $this->name));
    }

    private function export(DomainActionInterface $action)
    {
        foreach ($this->subscribers as $subscriber) {
            $action->subscribe($subscriber);
        }

        $action->alias($this->getAlias());

        return $action;
    }

    public function getAlias()
    {
        return isset($this->aliasName)
            ? $this->aliasName
            : $this->aliasName = $this->createAlias();
    }

    private function createAlias()
    {
        if (!class_exists($this->scope)) {
            return '';
        }

        $reflection = new \ReflectionClass($this->scope);

        return strtolower($reflection->getShortName());
    }

    public function name($name)
    {
        if (trim((string)$this->name) !== '') {
            throw new RuntimeException("Name has already been set.");
        }

        return $this->export(new self($this->scope, $name));
    }

    public function alias($aliasName)
    {
        $this->aliasName = $aliasName;

        return $this;
    }

    public function subscribe(callable $callback)
    {
        $this->subscribers[] = $callback;
    }

    public function unsubscribe(callable $callback)
    {
        if (false !== $key = array_search($callback)) {
            unset($this->subscribers[$key]);
        }
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @return float
     */
    public function getTime()
    {
        return $this->time;
    }

    public function isBoundTo($scope)
    {
        if (is_object($scope)) {
            return is_a($scope, $this->getScope());
        }

        return (string)$scope === $this->getScope();
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    public function getFullName()
    {
        return $this->getAlias() . '.' . $this->getName();
    }

    public function getName()
    {
        return $this->name;
    }

    private function setScope($scope)
    {
        if ($scope === null) {
            return;
        }
        if (is_object($scope)) {
            $scope = get_class($scope);
        }

        $scope = (string)$scope;
        if (!class_exists($scope) && !interface_exists($scope)) {
            throw new \RuntimeException("Error seting scope '$scope'. Class or Interface not found.");
        }

        $this->scope = $scope;
    }

    private function setName($name)
    {
        $this->name = (string)$name;
    }

    private function setData(ValueObjectInterface $data = null)
    {
        $this->data = $data !== null
            ? $data
            : new \ArrayObject([]);
    }

    private function setTime()
    {
        $this->time = microtime(true);
    }

}