<?php
/**
 *
 *  * This file is part of the Tidal/WampWatch package.
 *  * (c) 2016 Timo Michna <timomichna/yahoo.de>
 *  *
 *  * For the full copyright and license information, please view the LICENSE
 *  * file that was distributed with this source code.
 *
 */

require_once __DIR__ . '/../../bootstrap.php';

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


