<?php

require_once __DIR__ . '/../bootstrap.php';

use Tidal\WampWatch\Exception\NoSuchProcedureException;

class NoSuchProcedureExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function test_topic_name_can_be_retrieved()
    {
        $e = new NoSuchProcedureException('foo');

        $this->assertSame('foo', $e->getProcedureName());
    }

    public function test_is_no_procecure_error()
    {

        $this->assertTrue(NoSuchProcedureException::isNoProcedureError('wamp.error.no_such_procedure'));
        $this->assertFalse(NoSuchProcedureException::isNoProcedureError('foo'));

    }

}


