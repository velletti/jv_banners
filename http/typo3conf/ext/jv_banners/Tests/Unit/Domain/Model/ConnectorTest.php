<?php
namespace JVE\JvBanners\Tests\Unit\Domain\Model;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use JVE\JvBanners\Domain\Model\Connector;
/**
 * Test case.
 *
 * @author Joerg Velletti <typo3@velletti.de>
 */
class ConnectorTest extends UnitTestCase
{
    /**
     * @var Connector
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new Connector();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getEventnameReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getEventname()
        );
    }

    /**
     * @test
     */
    public function setEventnameForStringSetsEventname()
    {
        $this->subject->setEventname('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'eventname',
            $this->subject
        );
    }
}
