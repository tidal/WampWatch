<?php
/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\tests\unit;


use React\Promise\Promise;
use Tidal\WampWatch\Stub\ClientSessionStub;
use Tidal\WampWatch\Subscription\Collection;

class SubscriptionCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ClientSessionStub
     */
    private $session;

    /**
     * @var Collection
     */
    private $collection;

    public function setup()
    {
        $this->session = new ClientSessionStub();
        $this->collection = new Collection($this->session);
    }


    // initial state tests

    public function test_at_construction_is_not_subscribed()
    {

        $this->assertFalse($this->collection->isSubscribed());

    }

    public function test_at_construction_is_not_subscribing()
    {

        $this->assertFalse($this->collection->isSubscribing());

    }

    // subscription tests

    public function test_subscribe_returns_promise()
    {

        $this->assertInstanceOf(Promise::class, $this->collection->subscribe());

    }

    public function test_is_not_subscribed_if_not_all_subscriptions_returned()
    {

        $this->collection->addSubscription('foo', $this->getEmptyFunc());
        $this->collection->addSubscription('bar', $this->getEmptyFunc());

        $this->collection->subscribe();

        $this->session->completeSubscription('foo');

        $this->assertFalse($this->collection->isSubscribed());

    }


    public function test_is_subscribing_if_not_all_subscriptions_returned()
    {

        $this->collection->addSubscription('foo', $this->getEmptyFunc());
        $this->collection->addSubscription('bar', $this->getEmptyFunc());

        $this->collection->subscribe();

        $this->session->completeSubscription('foo');

        $this->assertTrue($this->collection->isSubscribing());

    }

    public function test_is_subscribed_if_all_subscriptions_returned()
    {

        $this->collection->addSubscription('foo', $this->getEmptyFunc());
        $this->collection->addSubscription('bar', $this->getEmptyFunc());

        $this->collection->subscribe();

        $this->session->completeSubscription('foo', 1, 1);
        $this->session->completeSubscription('bar', 2, 2);

        $this->assertTrue($this->collection->isSubscribed(), "Collection should be subscribed.");

    }

    public function test_subscribing_updates_promise()
    {

        $callbackNr = 0;

        $this->collection->addSubscription('foo', $this->getEmptyFunc());
        $this->collection->addSubscription('bar', $this->getEmptyFunc());

        $this->collection->subscribe()->done($this->getEmptyFunc(), $this->getEmptyFunc(), function () use (&$callbackNr) {
            $callbackNr++;
        });

        $this->session->completeSubscription('foo', 1, 1);
        $this->session->completeSubscription('bar', 2, 2);

        $this->assertEquals(2, $callbackNr, "Collection should update 2 times.");

    }

    public function test_promise_update_returns_topic()
    {

        $topic = "";

        $this->collection->addSubscription('foo', $this->getEmptyFunc());

        $this->collection->subscribe()->done($this->getEmptyFunc(), $this->getEmptyFunc(), function ($t) use (&$topic) {
            $topic = $t;
        });

        $this->session->completeSubscription('foo', 1, 1);

        $this->assertEquals('foo', $topic, "Subscription update should return topic.");

    }

    public function test_promise_resolved_returns_subscriptions_list()
    {

        $subs = [];

        $this->collection->addSubscription('foo', $this->getEmptyFunc());
        $this->collection->addSubscription('bar', $this->getEmptyFunc());

        $this->collection->subscribe()->done(function ($s) use (&$subs) {
            $subs = $s;
        });

        $this->session->completeSubscription('foo', 1, 1);
        $this->session->completeSubscription('bar', 2, 2);

        $this->assertEquals(['foo' => 1, 'bar' => 2], $subs, "Subscription resolve should return list of subscriptions");

    }

    public function test_is_not_subscribed_after_unsubscribe()
    {

        $subscribed = true;

        $this->collection->addSubscription('foo', $this->getEmptyFunc());

        $this->collection->subscribe();
        $this->session->completeSubscription('foo', 1, 1);

        $this->collection->unsubscribe()->done(function () use (&$subscribed) {
            $subscribed = $this->collection->isSubscribed();
        });

        $this->assertFalse($subscribed, "Collection should not be subscribed after unsubscribe");

    }

    public function test_subscribe_promise_resolves_after_being_already_subscribed()
    {

        $result1 = null;
        $result2 = null;

        $this->collection->addSubscription('foo', $this->getEmptyFunc());
        $this->collection->addSubscription('bar', $this->getEmptyFunc());

        $this->collection->subscribe()->done(function ($res) use (&$result1) {
            $result1 = $res;
        });

        $this->session->completeSubscription('foo', 1, 1);
        $this->session->completeSubscription('bar', 2, 2);

        $this->collection->subscribe()->done(function ($res) use (&$result2) {
            $result2 = $res;
        });

        $this->assertTrue($result1 !== null);
        $this->assertEquals($result1, $result2, "Results should be the same even if subscriptions have already been resolved.");

    }


    private function getEmptyFunc()
    {
        return function () {
        };
    }

}
