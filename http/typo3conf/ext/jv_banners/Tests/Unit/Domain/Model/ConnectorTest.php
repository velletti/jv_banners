<?php
namespace JVE\JvBanners\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Joerg Velletti <typo3@velletti.de>
 */
class ConnectorTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \JVE\JvBanners\Domain\Model\Connector
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \JVE\JvBanners\Domain\Model\Connector();
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
