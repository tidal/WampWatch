<?php
/**
 *
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Test\Unit\Model;

use Tidal\WampWatch\Model\Topic;

class TopicTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Topic
     */
    private $topic;

    public function setUp()
    {
        $this->topic = new Topic('foo');
    }

    public function test_can_retrieve_uri()
    {
        $this->assertSame('foo', $this->topic->getUri());
    }

}
