<?php
/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Model;


use Guzzle\Common\Exception\RuntimeException;
use Psr\Http\Message\UriInterface;
use Rhumsaa\Uuid\Console\Exception;
use Tidal\WampWatch\Model\Contract;
use Tidal\WampWatch\Model\Property\Collection\HasRealmsTrait;
use Tidal\WampWatch\Model\Property\Scalar\HasUriTrait;
use Tidal\WampWatch\Model\Connection;
use Tidal\WampWatch\Model\Behavior\Property\HasCollectionsTrait;

class Router implements Contract\RouterInterface
{
    use HasCollectionsTrait;
    use HasRealmsTrait;
    use HasUriTrait;

    const DEFAULT_CONNECTION_CLS = '\Tidal\WampWatch\Model\Connection';

    private static $connectionFactory;

    /**
     * @var UriInterface
     */
    private $uriObject;

    public function __construct(UriInterface $uri)
    {
        $this->setUriObject($uri);
    }

    /**
     * @param UriInterface $uri
     */
    private function setUriObject(UriInterface $uri)
    {
        $this->uriObject = $uri;
        $this->setUri((string)$uri);
    }

    /**
     * @param callable $factory
     */
    public static function setConnectionFactory(callable $factory)
    {
        self::$connectionFactory = $factory;
    }

    /**
     * @return UriInterface
     */
    public function getUriObject()
    {
        return $this->uriObject;
    }

    /**
     * @param Contract\RealmInterface $realm
     *
     * @return Contract\ConnectionInterface
     */
    public function connect(Contract\RealmInterface $realm)
    {
        try {
            $factory = self::$connectionFactory;
            $connection = $factory($this, $realm);
            if (!is_a($connection, Contract\ConnectionInterface::class)) {
                throw new RuntimeException("Callable registered as factory for Connection did not return Connection instance");
            }

        } catch (Exception $e) {
            throw $e;
        }

        return $connection;
    }
}

Router::setConnectionFactory(function (Contract\RouterInterface $router, Contract\RealmInterface $realm) {
    return new Connection($router, $realm);
});