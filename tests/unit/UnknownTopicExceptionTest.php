<?php

require_once __DIR__ . '/../bootstrap.php';

use Tidal\WampWatch\Exception\UnknownTopicException;

class UnknownUnknownTopicExceptionTest extends PHPUnit_Framework_TestCase
{

    public function test_topic_name_can_be_retrieved()
    {
        $e = new UnknownTopicException('foo');

        $this->assertSame('foo', $e->getTopicName());
    }


}
