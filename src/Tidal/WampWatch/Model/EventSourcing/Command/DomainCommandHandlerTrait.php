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

/**
 * Created by PhpStorm.
 * User: Timo
 * Date: 27.07.2016
 * Time: 20:48
 */

namespace src\Tidal\WampWatch\Model\CommandSourcing\CommandHandling;


trait DomainCommandHandlerTrait
{

    private $commands = [];

    private $domainCommand;

    private function handleNamedCommand(DomainCommandInterface $command)
    {
        if($command->getScope() !== self::class){

            return;
        }

        if(!$this->hasCommand($command->getName())){

            return;
        }

        $this->getCommand($command->getName())->publish($command->getData());
    }

    public function listen(DomainCommandInterface $command, callable $callback)
    {
        if(!$this->hasCommand($command->getName())){
            return;
        }
        if($command->getScope() !== self::class){
            return;
        }

        $command->subscribe($callback);
    }

    protected function exposeCommand($name)
    {
        return $this->commands[$name] = $this->getDomainCommand()->name($name);
    }

    protected function exposeCommands(array $names)
    {
        foreach ($names as  $name){
            $this->exposeCommand((string) $name);
        }
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
    protected function hasCommand($name)
    {
        return isset($this->commands[$name]);
    }

    /**
     * @return DomainCommandInterface
     */
    private function getDomainCommand()
    {
        return isset($this->domainCommand)
            ? $this->domainCommand
            : $this->domainCommand = DomainCommand::bind(self::class);
    }

    /**
     * @param mixed $command
     */
    public function handle($command)
    {
        $this->handleWithMethod($command);
    }



    private function handleDomainCommand($command)
    {

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

}