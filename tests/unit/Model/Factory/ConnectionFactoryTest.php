<?php
/**
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\Unit\Model\Factory;

use Tidal\WampWatch\Model\Factory\ConnectionFactory;
use Tidal\WampWatch\Test\Unit\Model\HasSessionTrait;
use Tidal\WampWatch\Test\Unit\Model\HasRouterTrait;
use Tidal\WampWatch\Test\Unit\Model\HasRealmTrait;
use Tidal\WampWatch\Model\Connection;
use PHPUnit_Framework_TestCase;
use RuntimeException;

class ConnectionFactoryTest extends PHPUnit_Framework_TestCase
{
    use HasSessionTrait;
    use HasRouterTrait;
    use HasRealmTrait;

    /**
     * @var ConnectionFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = ConnectionFactory::get(
            $this->getRouterMock()
        );
    }

    public function test_can_get_instance_for_router()
    {
        $this->assertInstanceOf(ConnectionFactory::class, $this->factory);
    }

    public function test_can_select_realm()
    {
        $factory = $this->factory->select(
            $this->getRealmMock()
        );
        $this->assertInstanceOf(ConnectionFactory::class, $factory);
    }

    public function test_select_returns_new_instance()
    {
        $factory = $this->factory->select(
            $this->getRealmMock()
        );

        $this->assertNotEquals($this->factory, $factory);
    }

    public function test_can_establish_session()
    {
        $factory = $this->factory->establish(
            $this->getSessionMock()
        );
        $this->assertInstanceOf(ConnectionFactory::class, $factory);
    }

    public function test_select_establish_new_instance()
    {
        $factory = $this->factory->establish(
            $this->getSessionMock()
        );

        $this->assertNotEquals($this->factory, $factory);
    }

    public function test_can_create()
    {
        $connection = $this->factory->select(
            $this->getRealmMock()
        )->establish(
            $this->getSessionMock()
        )->create();

        $this->assertInstanceOf(Connection::class, $connection);
    }

    public function test_uses_given_dependencies()
    {
        $router = $this->getRouterMock();
        $realm = $this->getRealmMock();
        $session = $this->getSessionMock();

        $connection = ConnectionFactory::get(
            $router
        )->select(
            $realm
        )->establish(
            $session
        )->create();

        $this->assertEquals($router, $connection->getRouter());
        $this->assertEquals($realm, $connection->getRealm());
        $this->assertEquals($session, $connection->getSession());
    }

    public function test_create_throws_exception_without_realm()
    {
        $this->setExpectedException(
            RuntimeException::class
        );

        $this->factory->establish(
            $this->getSessionMock()
        )->create();
    }
}
