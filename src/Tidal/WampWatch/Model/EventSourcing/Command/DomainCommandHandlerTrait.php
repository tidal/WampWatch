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


trait DomainCommandHandlerTrait
{

    private $commands = [];

    private $domainCommand;

    /**
     * @param mixed $command
     */
    public function handleCommand($command)
    {
        if (is_a($command, DomainCommandInterface::class)) {
            $this->handleNamedCommand($command);

            return;
        }

        $this->handleWithMethod($command);
    }

    private function handleNamedCommand(DomainCommandInterface $command)
    {


        if ($command->getScope() !== static::class && !is_a($this, $command->getScope())) {

            //return;
        }



        if(!$this->hasCommand($command->getName())){

            return;
        }

        $this->getCommand($command->getName())->issue($command->getData());
    }

    protected function hasCommand($name)
    {
        return isset($this->commands[$name]);
    }

    /**
     * @param string $name
     *
     * @return DomainCommandInterface
     */
    protected function getCommand($name)
    {
        if(!$this->hasCommand($name)){
            throw new \OutOfBoundsException("No command '$name' exposed.");
        }

        return $this->commands[$name];
    }

    private function handleWithMethod($command)
    {
        $method = $this->getHandleMethod($command);

        if (! method_exists($this, $method)) {
            return;
        }

        $this->$method($command);
    }

    private function getHandleMethod($command)
    {
        if (! is_object($command)) {
            throw new CommandNotAnObjectException();
        }

        $classParts = explode('\\', get_class($command));

        return 'handle' . end($classParts);
    }

    protected function exposeCommands(array $names)
    {
        foreach ($names as $name) {
            $this->exposeCommand((string)$name);
        }
    }

    protected function exposeCommand($name)
    {
        return $this->commands[$name] = $this->getDomainCommand()->name($name);
    }

    /**
     * @return DomainCommandInterface
     */
    private function getDomainCommand()
    {
        return isset($this->domainCommand)
            ? $this->domainCommand
            : $this->domainCommand = DomainCommand::create()->bind(static::class);
    }

}