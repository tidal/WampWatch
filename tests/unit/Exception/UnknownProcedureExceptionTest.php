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

require_once __DIR__ . '/../../bootstrap.php';

use Tidal\WampWatch\Exception\UnknownProcedureException;
use PHPUnit_Framework_TestCase;

class UnknownProcedureExceptionTest extends PHPUnit_Framework_TestCase
{
    public function test_procedure_name_can_be_retrieved()
    {
        $e = new UnknownProcedureException('foo');

        $this->assertSame('foo', $e->getProcedureName());
    }
}
