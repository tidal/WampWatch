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


namespace Tidal\WampWatch\Model\EventSourcing\Command;

use Tidal\WampWatch\Model\EventSourcing\ValueObject\ValueObjectInterface;
use Tidal\WampWatch\Model\EventSourcing\Action\DomainActionTrait;


class DomainCommand implements DomainCommandInterface
{
    use DomainActionTrait;

    protected function __construct($scope = null, $name = '', ValueObjectInterface $data = null)
    {
        $this->setScope($scope);
        $this->setName($name);
        $this->setData($data);
        $this->setTime();
    }

    public static function create($name = '')
    {
        return new self(null, $name);
    }

    public function issue(ValueObjectInterface $data)
    {
        $command = new self($this->scope, $this->name, $data);
        $command->alias($this->getAlias());

        foreach ($this->subscribers as $subscriber) {
            $subscriber($command);
        }

        return $command;
    }


}