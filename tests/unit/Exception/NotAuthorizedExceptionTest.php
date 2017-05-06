<?php
/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Test\Unit\Exception;

use Tidal\WampWatch\Exception\NotAuthorizedException;
use PHPUnit_Framework_TestCase;

class NotAuthorizedExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var NotAuthorizedException
     */
    private $exception;

    public function setUp()
    {
        $this->exception = new NotAuthorizedException('foo', 'bar');
    }

    public function test_construct_sets_topic_name()
    {
        $this->assertEquals('foo', $this->exception->getTopicName());
    }

    public function test_construct_sets_error_message()
    {
        $this->assertEquals('bar', $this->exception->getMessage());
    }

    public function test_construct_sets_default_error_message()
    {
        $exception = new NotAuthorizedException('foo');
        $this->assertEquals(NotAuthorizedException::DEFAULT_ERROR_MSG, $exception->getMessage());
    }

    public function test_not_authorized_error_is_correct()
    {
        $this->assertTrue(NotAuthorizedException::isNotAuthrorizedError('wamp.error.not_authorized'));
    }
}
