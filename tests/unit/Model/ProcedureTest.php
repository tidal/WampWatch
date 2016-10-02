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

namespace tests\unit\Model;

use Tidal\WampWatch\Model\Procedure;


class ProcedureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Procedure
     */
    private $procedure;

    public function setUp()
    {
        $this->procedure = new Procedure('foo');
    }

    public function test_can_retrieve_uri()
    {
        $this->assertSame('foo', $this->procedure->getUri());
    }
}
