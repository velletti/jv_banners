<?php
namespace JVE\JvBanners\Controller;

use JVE\JvBanners\Domain\Model\Banner;
use JVE\JvBanners\Domain\Model\Connector;
use JVE\JvBanners\Domain\Repository\BannerRepository;
use JVE\JvBanners\Domain\Repository\ConnectorRepository;
use JVE\JvBanners\Utility\AssetUtility;
use JVE\JvEvents\Domain\Model\Category;
use JVE\JvEvents\Domain\Model\Event;
use JVE\JvEvents\Domain\Repository\EventRepository;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/***
 *
 * This file is part of the "Banner Guthaben" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Joerg Velletti <typo3@velletti.de>
 *
 ***/

/**
 * ConnectorController
 */
class ConnectorController extends ActionController
{
    /**
     * connectorRepository
     *
     * @var ConnectorRepository
     */
    protected $connectorRepository = null;



    /**
     * persistencemanager
     *
     * @var PersistenceManager
     */
    protected $persistenceManager = NULL ;


    /**
     * BannerRepository
     *
     * @var BannerRepository;
     */
    protected $bannerRepository = null;

    /**
     * @var EventRepository
     */
    private $eventRepository;

    public function initializeAction()
    {
        parent::initializeAction();
        $this->bannerRepository = $this->objectManager->get("JVE\\JvBanners\\Domain\\Repository\\BannerRepository");
        $this->eventRepository = $this->objectManager->get("JVE\\JvEvents\\Domain\\Repository\\EventRepository");
        $this->connectorRepository = $this->objectManager->get("JVE\\JvBanners\\Domain\\Repository\\ConnectorRepository");
        $this->persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
    }
    /**
     * action Dummy
     *
     * @return void
     */
    public function dummyAction()
    {
    }
    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $connectors = $this->connectorRepository->findAll();
        $this->view->assign('connectors', $connectors);
    }

    /**
     * action new
     *
     * @return void
     */
    public function newAction()
    {

    }

    /**
     * action create
     *
     * @param Event $event
     * @return void
     * @throws NoSuchArgumentException
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     * @throws FileDoesNotExistException
     * @throws IllegalObjectTypeException
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("event")
     */
    public function createAction(Event $event)
    {
        if( $this->request->hasArgument("returnPid")) {
            $returnUid = $this->request->getArgument("returnPid");
        }
        if( !$event || !$returnUid ) {
            $this->addFlashMessage('Missing Arguments event and returnUid! ', AbstractMessage::ERROR);
            try {
                $this->redirect("show", "Event", "JvEvents");
            } catch (StopActionException $e) {
            } catch (UnsupportedRequestTypeException $e) {
            }

        }

        /** @var Banner $banner */
        $banner = GeneralUtility::makeInstance("JVE\\JvBanners\\Domain\\Model\\Banner") ;

        $cat = $event->getEventCategory() ;
        $event->setTopEvent(1) ;
        // banner Pid for Homepage = 56
        $pageId = 56 ;
        if( $cat ) {
            /** @var Category $category */
            foreach ($cat as $category ) {
                if( $category->getUid() == 2 ) {
                    // banner Pid for private lessons = 135
                    $pageId = 135 ;
                }
            }
        }
        $banner->setPid($pageId ) ;


        $banner->setTitle( $event->getName());
        $banner->setType(0); // 0 = image, 1 = html Banner
        $banner->setDescription($event->getTeaser());
        $html = $event->getStartDate()->format("d.m.Y") . "<br>\n" ;
        $html .= date( "H:i" , $event->getStartTime() ) . "-" ;
        $html .= date( "H:i" , $event->getEndTime() ) . "<br>\n" ;
        if( $event->getLocation() ) {
            $html .= $event->getLocation()->getCity() ;
        }


        $banner->setHtml($html);
        $banner->setImpressionsMax(6000);
        $banner->setClicksMax(400);
        $banner->setversionedUid($event->getUid());
        $banner->setLanguage(-1);
        $banner->setLink($event->getUId() );
        // $banner->setAssets();
        $sDateDiff = new \DateInterval("P8D") ;

        $accessStart = new \DateTime(  ) ;
        $accessStart->setTimestamp( $event->getStartDate()->getTimestamp()) ;


        $banner->setStarttime($accessStart->sub($sDateDiff)->getTimestamp()) ;

        /*   // set a banner but 1 day before event sotps
        $eDateDiff = new \DateInterval("P1D") ;
        $accessEnd = new \DateTime(  ) ;
        $accessEnd->setTimestamp( $event->getStartDate()->getTimestamp()) ;

        $banner->setEndtime($accessEnd->sub($eDateDiff)->getTimestamp() ) ;
        */

        // sets end Banner to midnight when event is on same day..
        $accessEnd = new \DateTime(  ) ;
        $accessEnd->setTimestamp( $event->getStartDate()->getTimestamp()) ;

        $banner->setEndtime($accessEnd->getTimestamp() ) ;

        $link = $this->uriBuilder->reset()
        ->setTargetPageUid($returnUid )
        ->uriFor(
            'show',
            array("event" => $event->getUid() ),
            'Event',
            'JvEvents') ;


        // create SysFile reference
        // uid_Local = Image uid_foreign = event ID wird Banner Id
        $assetData = AssetUtility::loadSysFileReference($event->getUid() , "tx_jvevents_domain_model_event" , "teaser_image") ;

        if( is_array($assetData )) {
            $asset = AssetUtility::generateAssetfromSysFileReference("tx_sfbanners_domain_model_banner", "assets", $assetData, "sys_file", $link);

            /** @var ResourceFactory $factory */
            $factory = GeneralUtility::makeInstance(ResourceFactory::class) ;

            // ******* Maybe we need to make Uid of storage (fileadmin/userupload/ configurable !
            $storage = $factory->getStorageObject(1);

            /** @var File $file */
            $file = $storage->getResourceFactoryInstance()->getFileObject($assetData['uid_local']);
            $asset->setFile($file);

            $banner->addAsset($asset);
            $this->bannerRepository->add($banner);
            $this->eventRepository->update($event);

            $this->persistenceManager->persistAll();

            $this->addFlashMessage('Banner for event ' . $event->getUid() . " - " . $event->getName() . " Start: " . date("D.m.Y", $banner->getStarttime()), 'Banner created', AbstractMessage::OK);
        } else {
            $this->addFlashMessage('Banner for event ' . $event->getUid() . " - Could not find Teaser Image ", AbstractMessage::ERROR);

        }

        $this->redirect("show" , "Event" , "JvEvents", ['event' => $event->getUid() ] , $returnUid) ;
    }

    /**
     * action edit
     *
     * @param Connector $connector
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("connector")
     * @return void
     */
    public function editAction(Connector $connector)
    {
       // $this->view->assign('connector', $connector);
    }

    /**
     * action update
     *
     * @param Connector $connector
     * @return void
     * @throws IllegalObjectTypeException
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     * @throws UnknownObjectException
     */
    public function updateAction(Connector $connector)
    {
        $this->addFlashMessage('The object NOT was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', AbstractMessage::WARNING);
      //  $this->connectorRepository->update($connector);
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param Connector $connector
     * @return void
     * @throws IllegalObjectTypeException
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     */
    public function deleteAction(Connector $connector)
    {
        $this->addFlashMessage('The object was NOT deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', AbstractMessage::WARNING);
     //   $this->connectorRepository->remove($connector);
        $this->redirect('list');
    }

    /**
     * action disable
     *
     * @return void
     */
    public function disableAction()
    {

    }

    /**
     * action enable
     *
     * @return void
     */
    public function enableAction()
    {

    }

    /**
     * action reducePoints
     *
     * @return void
     */
    public function reducePointsAction()
    {

    }

    /**
     * action addPoints
     *
     * @return void
     */
    public function addPointsAction()
    {

    }


}
