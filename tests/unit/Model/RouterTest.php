<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\unit\Model;

use Tidal\WampWatch\Model\Connection;
use Tidal\WampWatch\Model\Router;
use Tidal\WampWatch\Model\Contract\ConnectionInterface;
use RuntimeException;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    use HasRealmTrait;

    /**
     * @var Router
     */
    private $router;

    public function setUp()
    {
        $this->router = new Router('foo://bar');
    }

    public function test_can_retrieve_uri()
    {
        $this->assertSame('foo://bar', $this->router->getUri());
    }

    public function test_can_connect()
    {
        $connection = $this->router->connect(
            $this->getRealmMock()
        );

        $this->assertInstanceOf(ConnectionInterface::class, $connection);
    }

    public function test_can_set_connection_factory()
    {
        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->router->setConnectionFactory(function () use ($connectionMock) {
            return $connectionMock;
        });

        $connection = $this->router->connect(
            $this->getRealmMock()
        );

        $this->assertSame($connectionMock, $connection);
    }

    public function test_incorrect_connection_factory_throws_exception()
    {
        $this->setExpectedException(
            RuntimeException::class
        );

        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->router->setConnectionFactory(function () use ($connectionMock) {
            return new \stdClass();
        });

        $this->router->connect(
            $this->getRealmMock()
        );
    }

    public function test_can_add_realm()
    {
        $realm = $this->getRealmMock();
        $name = $realm->getName();
        $this->router->addRealm($realm);

        $this->assertTrue($this->router->hasRealm($name));
    }

    public function test_can_remove_realm()
    {
        $realm = $this->getRealmMock();
        $name = $realm->getName();
        $this->router->addRealm($realm);
        $this->router->removeRealm($name);

        $this->assertFalse($this->router->hasRealm($name));
    }

    public function test_can_retrieve_realm()
    {
        $realm = $this->getRealmMock();
        $name = $realm->getName();
        $this->router->addRealm($realm);

        $this->assertEquals($realm, $this->router->getRealm($name));
    }

    public function test_can_list_realms()
    {
        $realms = [
            $this->getRealmMock('foo'),
            $this->getRealmMock('bar'),
            $this->getRealmMock('baz'),
        ];

        foreach ($realms as $realm) {
            $this->router->addRealm($realm);
        }

        $x = 0;
        foreach ($this->router->listRealms() as $name => $realm) {
            $this->assertEquals($realms[$x], $realm);
            $this->assertEquals($realms[$x], $this->router->getRealm($name));
            ++$x;
        }
    }
}
