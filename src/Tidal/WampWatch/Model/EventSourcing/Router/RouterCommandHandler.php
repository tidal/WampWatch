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


namespace Tidal\WampWatch\Model\EventSourcing\Router;


use Tidal\WampWatch\Model\EventSourcing\Router as RouterEntity;
use Tidal\WampWatch\Model\EventSourcing\Command\AbstractDomainCommandHandler;
use Tidal\WampWatch\Model\EventSourcing\Repository\RepositoryInterface;


class RouterCommandHandler extends AbstractDomainCommandHandler
{

    public function __construct(RepositoryInterface $repository)
    {
        parent::__construct($repository);

        $this->exposeCommands([
            'router.start',
            'router.expose.realm'
        ]);

        $this->getCommand('router.start')->subscribe(function ($command) {
            echo "\n\nCOMMAND 'router.start' : \n\n";
            $router = RouterEntity::create()->start($command->data->uri);
            $this->getRepository()->save($router);
        });

        $this->getCommand('router.expose.realm')->subscribe(function ($command) {
            echo "\n\nCOMMAND 'router.expose.realm' :" . print_r($command->data, 1) . " \n\n";
            /** @var $router RouterEntity */
            $router = $this->getRepository()->load($command->data->uri);
            $event = $router->expose($command->data->realm);
            //$this->getRepository()->g
            $this->getRepository()->save($router);
            //$this->getRepository()->save($router);
        });
    }


}