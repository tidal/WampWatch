<?php
/**
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\unit\Model;

use Tidal\WampWatch\Model\Subscription;

class SubscriptionTest extends \PHPUnit_Framework_TestCase
{
    use HasSessionTrait;
    use HasTopicTrait;

    /**
     * @var Subscription
     */
    private $subscription;

    public function setUp()
    {
        $this->subscription = new Subscription(
            321,
            $this->getSessionMock(),
            $this->getTopicMock()
        );
    }

    public function test_can_retrieve_id()
    {
        $this->assertSame(321, $this->subscription->getId());
    }

    public function test_can_retrieve_session()
    {
        $this->assertSame('baz', $this->subscription->getSession()->getId());
    }

    public function test_can_retrieve_topic()
    {
        $this->assertSame('foo', $this->subscription->getTopic()->getUri());
    }
}
