<?php
/**
 * This file is part of the Tidal/WampWatch package.
 *  (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\Unit\Model;

use Tidal\WampWatch\Model\Realm;
use Tidal\WampWatch\Model\Router;

class RealmTest extends \PHPUnit_Framework_TestCase
{
    use HasRouterTrait;
    use HasProcedureTrait;
    use HasTopicTrait;

    const REALM_NAME = 'foo-realm';

    /**
     * @var Realm
     */
    private $realm;

    /**
     * @var Router
     */
    private $router;

    public function setUp()
    {
        $this->router = $this->getRouterMock();

        $this->realm = new Realm(
            self::REALM_NAME,
            $this->router
        );
    }

    public function test_can_retrieve_name()
    {
        $this->assertSame(self::REALM_NAME, $this->realm->getName());
    }

    public function test_can_retrieve_router()
    {
        $this->assertSame($this->router, $this->realm->getRouter());
    }

    public function test_can_add_procedure()
    {
        $procedure = $this->getProcedureMock();
        $uri = $procedure->getUri();
        $this->realm->addProcedure($procedure);

        $this->assertTrue($this->realm->hasProcedure($uri));
    }

    public function test_can_remove_procedure()
    {
        $procedure = $this->getProcedureMock();
        $uri = $procedure->getUri();
        $this->realm->addProcedure($procedure);
        $this->realm->removeProcedure($uri);

        $this->assertFalse($this->realm->hasProcedure($uri));
    }

    public function test_can_retrieve_procedure()
    {
        $procedure = $this->getProcedureMock();
        $uri = $procedure->getUri();
        $this->realm->addProcedure($procedure);

        $this->assertEquals($procedure, $this->realm->getProcedure($uri));
    }

    public function test_can_list_procedures()
    {
        $procedures = [
            $this->getProcedureMock('foo'),
            $this->getProcedureMock('bar'),
            $this->getProcedureMock('baz')
        ];

        foreach ($procedures as $procedure) {
            $this->realm->addProcedure($procedure);
        }

        $x = 0;
        foreach ($this->realm->listProcedures() as $uri => $procedure) {
            $this->assertEquals($procedures[$x], $procedure);
            $this->assertEquals($procedures[$x], $this->realm->getProcedure($uri));
            $x++;
        }
    }

    public function test_can_add_topic()
    {
        $topic = $this->getTopicMock();
        $uri = $topic->getUri();
        $this->realm->addTopic($topic);

        $this->assertTrue($this->realm->hasTopic($uri));
    }

    public function test_can_remove_topic()
    {
        $topic = $this->getTopicMock();
        $uri = $topic->getUri();
        $this->realm->addTopic($topic);
        $this->realm->removeTopic($uri);

        $this->assertFalse($this->realm->hasTopic($uri));
    }

    public function test_can_retrieve_topic()
    {
        $topic = $this->getTopicMock();
        $uri = $topic->getUri();
        $this->realm->addTopic($topic);

        $this->assertEquals($topic, $this->realm->getTopic($uri));
    }

    public function test_can_list_topics()
    {
        $topics = [
            $this->getTopicMock('foo'),
            $this->getTopicMock('bar'),
            $this->getTopicMock('baz')
        ];

        foreach ($topics as $topic) {
            $this->realm->addTopic($topic);
        }

        $x = 0;
        foreach ($this->realm->listTopics() as $uri => $topic) {
            $this->assertEquals($topics[$x], $topic);
            $this->assertEquals($topics[$x], $this->realm->getTopic($uri));
            $x++;
        }
    }

}
