<?php
namespace JVE\JvBanners\Tests\Unit\Controller;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use JVE\JvBanners\Controller\ConnectorController;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use JVE\JvBanners\Domain\Repository\ConnectorRepository;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use JVE\JvBanners\Domain\Model\Connector;
/**
 * Test case.
 *
 * @author Joerg Velletti <typo3@velletti.de>
 */
class ConnectorControllerTest extends UnitTestCase
{
    /**
     * @var ConnectorController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(ConnectorController::class)
            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllConnectorsFromRepositoryAndAssignsThemToView()
    {

        $allConnectors = $this->getMockBuilder(ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connectorRepository = $this->getMockBuilder(ConnectorRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $connectorRepository->expects(self::once())->method('findAll')->will(self::returnValue($allConnectors));
        $this->inject($this->subject, 'connectorRepository', $connectorRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('connectors', $allConnectors);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenConnectorToConnectorRepository()
    {
        $connector = new Connector();

        $connectorRepository = $this->getMockBuilder(ConnectorRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $connectorRepository->expects(self::once())->method('add')->with($connector);
        $this->inject($this->subject, 'connectorRepository', $connectorRepository);

        $this->subject->createAction($connector);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenConnectorToView()
    {
        $connector = new Connector();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('connector', $connector);

        $this->subject->editAction($connector);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenConnectorInConnectorRepository()
    {
        $connector = new Connector();

        $connectorRepository = $this->getMockBuilder(ConnectorRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $connectorRepository->expects(self::once())->method('update')->with($connector);
        $this->inject($this->subject, 'connectorRepository', $connectorRepository);

        $this->subject->updateAction($connector);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenConnectorFromConnectorRepository()
    {
        $connector = new Connector();

        $connectorRepository = $this->getMockBuilder(ConnectorRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $connectorRepository->expects(self::once())->method('remove')->with($connector);
        $this->inject($this->subject, 'connectorRepository', $connectorRepository);

        $this->subject->deleteAction($connector);
    }
}
