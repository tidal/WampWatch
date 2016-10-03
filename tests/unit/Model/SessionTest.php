<?php
/**
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\Unit\Model;

use Tidal\WampWatch\Model\Session;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Session
     */
    private $session;

    public function setUp()
    {
        $this->session = new Session('foo');
    }

    public function test_can_retrieve_id()
    {
        $this->assertSame('foo', $this->session->getId());
    }
}
