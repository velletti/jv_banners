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
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Mail\MailMessage;
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
        $banner->setImpressionsMax(10000);
        $banner->setClicksMax(500);
        $banner->setversionedUid($event->getUid());
        $banner->setLanguage(-1);
        $banner->setLink($event->getUId() );

        // delete any existing banner for same event
        /** @var \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance( "TYPO3\\CMS\\Core\\Database\\ConnectionPool");

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tx_sfbanners_domain_model_banner') ;
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $row = $queryBuilder ->select('uid' , 'starttime', 'endtime', 'impressions','clicks'   ) ->from('tx_sfbanners_domain_model_banner')
            ->where( $queryBuilder->expr()->eq('link', $queryBuilder->createNamedParameter( $event->getUId() , \PDO::PARAM_INT)) )
            ->andWhere( $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0 , \PDO::PARAM_INT)) )
            ->andWhere( $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0 , \PDO::PARAM_INT)) )
            ->orderBy("endtime" , "DESC")
            ->setMaxResults(1)
            ->execute()
            ->fetchAssociative();


        if ( $row) {
            $banner->setImpressions($row['impressions']);
            $banner->setClicks($row['clicks']);

            $banner->setImpressionsMax((int)$row['impressions'] + 10000);
            $banner->setClicksMax((int)$row['clicks'] + 500);

            $queryBuilder->update('tx_sfbanners_domain_model_banner')
                ->where($queryBuilder->expr()->eq('link', $queryBuilder->createNamedParameter($event->getUId(), \PDO::PARAM_INT)))
                ->set("deleted", "1")->execute();
        }


        // $banner->setAssets();


        $accessStart = new \DateTime(  ) ;
        $sDateDiff = new \DateInterval("P8D") ;
        $accessStart->setTimestamp( $event->getStartDate()->getTimestamp()) ;
        $banner->setStarttime($accessStart->sub($sDateDiff)->getTimestamp() + (3600)) ;

        $endTime = PHP_INT_MAX ;
        // with optional param startindays banner can start On same day ( if 1 ) or in one week ( f.e. 7 ) ,
        if( $this->request->hasArgument("startindays") ) {
            $startindays = intval ( $this->request->getArgument("startindays")) * ( 24 * 3600 )  + time()  - (25 * 3600 )  ;
            if ( $startindays < $banner->getStarttime()) {
                $banner->setStarttime( $startindays ) ;
            }
            $endTime = $banner->getStarttime() + ( 7 * 24 * 3600 ) ;
        }






        /*   // set a banner but 1 day before event stops + 20 hours  if week is not full
        $eDateDiff = new \DateInterval("P1D") ;
        $accessEnd = new \DateTime(  ) ;
        $accessEnd->setTimestamp( $event->getStartDate()->getTimestamp()) ;

        $banner->setEndtime($accessEnd->sub($eDateDiff)->getTimestamp() ) ;
        */

        // sets end Banner to midnight when event is on same day..
        $accessEnd = new \DateTime(  ) ;
        $accessEnd->setTimestamp( $event->getStartDate()->getTimestamp()) ;


        $banner->setEndtime($accessEnd->getTimestamp() ) ;

        // If banner start more than 7 days before event, (by param "startindays" )  need to stop it earlier
        if ( $endTime < $banner->getEndtime() ) {
            $banner->setEndtime( $endTime ) ;
        }
        if ( $event->getStartDate()->getTimestamp() - ( 2 * 24 * 3600 ) < time() ) {
            $banner->setEndtime($accessEnd->getTimestamp() + (20 * 3600 ) ) ;
        }

        // overrule all Start/stop setting if StartIndDays = -1 to be able to stop a banner

        if( $this->request->hasArgument("startindays") &&  $this->request->getArgument("startindays") == -1 ) {
            $banner->setStarttime(time() - 3600 * 24  - 60 );
            $banner->setEndtime(time() - 3600 * 24 );
        }

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
        $link = GeneralUtility::getIndpEnv("TYPO3_REQUEST_HOST") .   $link  ;
        $mailtext  =  "Banner: " . date( "d.m H:i " , $banner->getStarttime()) . " - " . date( "d.m H:i " , $banner->getEndtime()) .  "<br>\n" ;
        $mailtext  .= "Event: " . $event->getName() . "<br>\n" ;
        $mailtext .=  "Text: "  . $event->getTeaser() . "<br>\n"  . $banner->getHtml() . "<br>\n" . "<br>\n";
        $mailtext .=  "Link: <a href=\""  .$link. "\"> " . $link . "</a><br>\n" ;
        $mailtext .=  "Organizer: " . $event->getOrganizer()->getName() .  " " . $event->getOrganizer()->getEmail() . "<br>\n" . "<br>\n";
        $mailtext .=  "Shown: "  .$banner->getImpressions() . "/"  .$banner->getImpressionsMax() . " | Clicked: "  .$banner->getClicks() . "/"  .$banner->getClicksMax() . "<br>\n" ;


        $mail = new MailMessage();
        $mail->setFrom("nl@tangomuenchen.de");

        $mail->html($mailtext) ;
        $mail->text(strip_tags( $mailtext)) ;

        if( GeneralUtility::validEmail($event->getOrganizer()->getEmail())) {
            $mail->setSubject("[Banner] Banner aktiviert/activated " . date( "d.m H:i " , $banner->getStarttime()) . " - " . date( "d.m H:i " , $banner->getEndtime()) ) ;

            $mail->setTo($event->getOrganizer()->getEmail()) ;
            $mail->setCc("info@tangomuenchen.de");
        } else {
            $mail->setSubject("[Banner-NoMailTo] Banner aktiviert/activated " . date( "d.m H:i " , $banner->getStarttime()) . " - " . date( "d.m H:i " , $banner->getEndtime()) ) ;

            $mail->setTo("info@tangomuenchen.de");
        }
        $mail->send() ;

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
