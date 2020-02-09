<?php
namespace JVE\JvBanners\Controller;

use JVE\JvBanners\Domain\Model\Banner;
use JVE\JvBanners\Utility\AssetUtility;
use JVE\JvEvents\Domain\Model\Event;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

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
class ConnectorController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * connectorRepository
     *
     * @var \JVE\JvBanners\Domain\Repository\ConnectorRepository
     * @inject
     */
    protected $connectorRepository = null;



    /**
     * persistencemanager
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager = NULL ;


    /**
     * BannerRepository
     *
     * @var \JVE\JvBanners\Domain\Repository\BannerRepository;
     */
    protected $bannerRepository = null;

    public function initializeAction()
    {
        parent::initializeAction();
       // $this->bannerRepository = GeneralUtility::makeInstance("") ;
        $this->bannerRepository = $this->objectManager->get("JVE\\JvBanners\\Domain\\Repository\\BannerRepository");
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
     * @ignorevalidation $event
     * @return void
     */
    public function createAction(Event $event)
    {
        if( $this->request->hasArgument("returnPid")) {
            $returnUid = $this->request->getArgument("returnPid");
        }
        if( !$event || !$returnUid ) {
            $this->addFlashMessage('Missing Arguments event and returnUid! ', AbstractMessage::ERROR);
            $this->redirect("show" , "Event" , "JvEvents" ) ;

        }

        /** @var Banner $banner */
        $banner = GeneralUtility::makeInstance("JVE\\JvBanners\\Domain\\Model\\Banner") ;

        $banner->setPid(56) ;
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

        $eDateDiff = new \DateInterval("P1D") ;
        $accessEnd = new \DateTime(  ) ;
        $accessEnd->setTimestamp( $event->getStartDate()->getTimestamp()) ;

        $banner->setEndtime($accessEnd->sub($eDateDiff)->getTimestamp() ) ;

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
        $asset = AssetUtility::generateAssetfromSysFileReference( "tx_sfbanners_domain_model_banner" , "assets" , $assetData, "sys_file" , $link ) ;

        $storage = ResourceFactory::getInstance()->getStorageObject(1);

        /** @var \TYPO3\CMS\Core\Resource\File $file */
        $file = $storage->getResourceFactoryInstance()->getFileObject($assetData['uid_local']) ;
        $asset->setFile($file) ;

        $banner->addAsset($asset) ;
        $this->bannerRepository->add($banner) ;

        $this->persistenceManager->persistAll() ;

        $this->addFlashMessage('Banner for event ' . $event->getUid() . " - "  . $event->getName() . " Start: " . date( "D.m.Y" , $banner->getStarttime() ), 'Banner created', AbstractMessage::INFO);

        $this->redirect("show" , "Event" , "JvEvents", ['event' => $event->getUid() ] , $returnUid) ;
    }

    /**
     * action edit
     *
     * @param \JVE\JvBanners\Domain\Model\Connector $connector
     * @ignorevalidation $connector
     * @return void
     */
    public function editAction(\JVE\JvBanners\Domain\Model\Connector $connector)
    {
        $this->view->assign('connector', $connector);
    }

    /**
     * action update
     *
     * @param \JVE\JvBanners\Domain\Model\Connector $connector
     * @return void
     */
    public function updateAction(\JVE\JvBanners\Domain\Model\Connector $connector)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', AbstractMessage::WARNING);
        $this->connectorRepository->update($connector);
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param \JVE\JvBanners\Domain\Model\Connector $connector
     * @return void
     */
    public function deleteAction(\JVE\JvBanners\Domain\Model\Connector $connector)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', AbstractMessage::WARNING);
        $this->connectorRepository->remove($connector);
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
