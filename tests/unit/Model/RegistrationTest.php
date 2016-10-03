<?php
/**
 * This file is part of the Tidal/WampWatch package.
 * (c) 2016 Timo Michna <timomichna/yahoo.de>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tidal\WampWatch\Test\Unit\Model;

use Tidal\WampWatch\Model\Registration;

class RegistrationTest extends \PHPUnit_Framework_TestCase
{
    use HasSessionTrait;
    use HasProcedureTrait;

    /**
     * @var Registration
     */
    private $registration;

    public function setUp()
    {
        $this->registration = new Registration(
            321,
            $this->getSessionMock('baz'),
            $this->getProcedureMock('foo')
        );
    }

    public function test_can_retrieve_id()
    {
        $this->assertSame(321, $this->registration->getId());
    }

    public function test_can_retrieve_session()
    {
        $this->assertSame('baz', $this->registration->getSession()->getId());
    }

    public function test_can_retrieve_topic()
    {
        $this->assertSame('foo', $this->registration->getProcedure()->getUri());
    }
}
