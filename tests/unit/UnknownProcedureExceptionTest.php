<?php

require_once __DIR__ . '/../bootstrap.php';

use Tidal\WampWatch\Exception\UnknownProcedureException;

class UnknownProcedureExceptionTest extends PHPUnit_Framework_TestCase
{

    public function test_procedure_name_can_be_retrieved()
    {
        $e = new UnknownProcedureException('foo');

        $this->assertSame('foo', $e->getProcedureName());
    }


}
