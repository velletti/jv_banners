<?php
namespace JVelletti\JvBanners\Controller;

use Exception;
use JVelletti\JvEvents\Domain\Model\Organizer;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use JVelletti\JvBanners\Domain\Model\Banner;
use JVelletti\JvBanners\Domain\Model\Connector;
use JVelletti\JvBanners\Domain\Repository\BannerRepository;
use JVelletti\JvBanners\Domain\Repository\ConnectorRepository;
use JVelletti\JvBanners\Utility\AssetUtility;
use JVelletti\JvEvents\Domain\Model\Category;
use JVelletti\JvEvents\Domain\Model\Event;
use JVelletti\JvEvents\Domain\Repository\EventRepository;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Service\CacheService;

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

        $this->bannerRepository = GeneralUtility::makeInstance("JVelletti\\JvBanners\\Domain\\Repository\\BannerRepository") ;

        $this->eventRepository = GeneralUtility::makeInstance("JVelletti\\JvEvents\\Domain\\Repository\\EventRepository") ;
        $this->connectorRepository = GeneralUtility::makeInstance("JVelletti\\JvBanners\\Domain\\Repository\\ConnectorRepository") ;
        $this->persistenceManager = GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager") ;
    }
    /**
     * action Dummy
     *
     * @return void
     */
    public function dummyAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }
    /**
     * action list
     *
     * @return void
     */
    public function listAction(): ResponseInterface
    {
        $now = time() ;
        $endTime = $now + ( 3600 * 24 * 42) ;
        $banners = $this->bannerRepository->findActiveFutureBanners($endTime );
        $this->view->assign('banners', $banners);
        $this->view->assign('now', $now);
        $this->view->assign('endTime', $endTime);
        $this->view->assign('hundertPercent', $endTime - $now );
        return $this->htmlResponse();
    }

    /**
     * action new
     *
     * @return void
     */
    public function newAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * action create
     *
     * @param Event $event
     * @throws NoSuchArgumentException
     * @throws FileDoesNotExistException
     * @throws IllegalObjectTypeException
     * @IgnoreValidation("event")
     */
    public function createAction(Event $event)
    {
        if( $this->request->hasArgument("returnPid")) {
            $returnUid = $this->request->getArgument("returnPid");
        }


        if( !$event || !$returnUid ) {
            $this->addFlashMessage('Missing Arguments event and returnUid! ', AbstractMessage::ERROR);
            try {
                return $this->redirect("show", "Event", "JvEvents");
            } catch (Exception $e) {
            }

        }

        /** @var Banner $banner */
        $banner = GeneralUtility::makeInstance("JVelletti\\JvBanners\\Domain\\Model\\Banner") ;

        $cat = $event->getEventCategory() ;

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
        $linkPageName = "Learn / Lernen" ;
        if( $pageId == 56) {
            $linkPageName = "Dance / Tanzen" ;
            $event->setTopEvent(1) ;
        }
        $banner->setPid($pageId ) ;

        $banner->setTitle( $event->getName());

        if($event->getOrganizer()) {
            if ( !$this->hasUserAccess($event->getOrganizer())) {
                $this->addFlashMessage('NoAccess for Event ' . $event->getUid() . " ", ContextualFeedbackSeverity::ERROR);
                return $this->redirect("show" , "Event" , "JvEvents", ['event' => $event->getUid() ] , $returnUid) ;
            }
            $banner->setOrganizer( $event->getOrganizer()->getUid());
            if( $event->getOrganizer()->getAccessUsers()) {
                $users = GeneralUtility::trimExplode( "," , $event->getOrganizer()->getAccessUsers()) ;
                if( count($users) > 0 ) {
                    $banner->setFeUser( $users[0]);
                }
            }
        } else {
            $this->addFlashMessage('Organizer for Event ' . $event->getUid() . " ", ContextualFeedbackSeverity::ERROR);
            return $this->redirect("show" , "Event" , "JvEvents", ['event' => $event->getUid() ] , $returnUid) ;

        }

        $banner->setType(0); // 0 = image, 1 = html Banner
        $banner->setDescription($event->getTeaser());
        $html = $event->getStartDate()->format("d.m.Y") . "<br>\n" ;
        $html .= date( "H:i" , $event->getStartTime() ) . "-" ;
        $html .= date( "H:i" , $event->getEndTime() ) . "<br>\n" ;
        if( $event->getLocation() ) {
            $html .= $event->getLocation()->getCity() ;
        }


        $banner->setHtml($html);
        $banner->setImpressionsMax(20000);
        $banner->setClicksMax(1000);
        $banner->setversionedUid($event->getUid());
        $banner->setLanguage(-1);
        $banner->setLink((string)$event->getUId() );

        // delete any existing banner for same event
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance( "TYPO3\\CMS\\Core\\Database\\ConnectionPool");

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tx_sfbanners_domain_model_banner') ;
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $row = $queryBuilder ->select('uid' , 'starttime', 'endtime', 'impressions','clicks'   ) ->from('tx_sfbanners_domain_model_banner')
            ->where( $queryBuilder->expr()->eq('link', $queryBuilder->createNamedParameter( $event->getUId() , \PDO::PARAM_INT)) )
            ->andWhere( $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0 , \PDO::PARAM_INT)) )
            ->andWhere( $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0 , \PDO::PARAM_INT)) )
            ->orderBy("endtime" , "DESC")
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();


        if ( $row) {
            $banner->setImpressions($row['impressions']);
            $banner->setClicks($row['clicks']);

            $banner->setImpressionsMax((int)$row['impressions'] + 10000);
            $banner->setClicksMax((int)$row['clicks'] + 500);

            $queryBuilder->update('tx_sfbanners_domain_model_banner')
                ->where($queryBuilder->expr()->eq('link', $queryBuilder->createNamedParameter($event->getUId(), \PDO::PARAM_INT)))
                ->set("deleted", "1")->executeStatement();
        }


        // $banner->setAssets();


        $accessStart = new \DateTime(  ) ;
        $sDateDiff = new \DateInterval("P8D") ;
        $accessStart->setTimestamp( $event->getStartDate()->getTimestamp()) ;
        $banner->setStarttime($accessStart->sub($sDateDiff)->getTimestamp() + (3600)) ;

        $endTime = PHP_INT_MAX ;
        // with optional param startindays banner can start On same day ( if 1 ) or in one week ( f.e. 7 ) ,
        $bannerDate = 1 ;
        if( $this->request->hasArgument("startindays") ) {
            $bannerDate = intval ( $this->request->getArgument("startindays")) ;
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
        // final again Set sart day in 2 / 3 days, even if week is not full
        if( $this->request->hasArgument("startindays") ) {
            $startindays = intval ( $this->request->getArgument("startindays")) * ( 24 * 3600 )  + time()  - (25 * 3600 )  ;
            if ( $startindays < $banner->getEndtime()) {
                $banner->setStarttime( $startindays ) ;
            }
        }
        // never set startdate in past
        if(   $banner->getStarttime() < ( time() - 7200 ) ) {
            $banner->setStarttime( time()  ) ;
        }

        // overrule all Start/stop setting if StartIndDays = -1 to be able to stop a banner
        $messageSubject = "Banner created/updated - " ;
        if( $this->request->hasArgument("startindays") &&  $this->request->getArgument("startindays") == -1 ) {
            $banner->setStarttime(time() - (3600 * 24  - 60 )); // gestern zählt noch für max Banner
            $banner->setEndtime(time() - 3600 * 24 );
            $messageSubject = "Banner stopped - " ;
            if ( $banner->getImpressions() * 2 < $banner->getImpressionsMax() ) {
                $banner->setHidden(1);
                $messageSubject = "Banner deleted - " ;
            }

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
        $imageFrom = "Event" ;
        if( !is_array($assetData )) {
            $imageFrom = "Location" ;

            $assetData = AssetUtility::loadSysFileReference($event->getLocation()->getUid() , "tx_jvevents_domain_model_location" , "teaser_image") ;
        }
        if( !is_array($assetData )) {
            $imageFrom = "Organizer" ;
            $assetData = AssetUtility::loadSysFileReference($event->getOrganizer()->getUid() , "tx_jvevents_domain_model_organizer" , "teaser_image") ;
        }
        $cacheService = GeneralUtility::makeInstance( CacheService::class) ;
        $cacheService->clearPageCache( [(int)$returnUid] );

        if( is_array($assetData )) {
            $asset = AssetUtility::generateAssetfromSysFileReference("tx_sfbanners_domain_model_banner", "assets", $assetData, $link);

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

            $assetDataNew = AssetUtility::loadSysFileReference($banner->getUid() , "tx_sfbanners_domain_model_banner" , "assets") ;
            if( $assetDataNew && $assetDataNew['uid'] > 0  && $assetData['uid_local'] ) {

                /** @var ConnectionPool $connectionPool */
                $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

                /** @var Connection $dbConnectionForSysRef */
                $dbConnectionForSysRef = $connectionPool->getConnectionForTable('sys_file_reference');

                /** @var QueryBuilder $queryBuilder */
                $queryBuilder = $dbConnectionForSysRef->createQueryBuilder();

                $queryBuilder
                    ->update('sys_file_reference')
                    ->set( 'uid_local' ,  intval( $assetData['uid_local'] ))
                    ->set( 'link' ,  $link )
                    ->where( $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($assetDataNew['uid'] , \PDO::PARAM_INT )) )
                    ->executeStatement();

            }
            $this->addFlashMessage('Banner for event ' . $event->getUid() . " - " . $event->getName() . " Start: " . date("D.m.Y", $banner->getStarttime()), 'Banner created', ContextualFeedbackSeverity::OK);
        } else {
            $this->addFlashMessage('Banner for event ' . $event->getUid() . " - Could not find Teaser Image ", ContextualFeedbackSeverity::ERROR);
            $imageFrom = "Not Found" ;
        }

        $link = GeneralUtility::getIndpEnv("TYPO3_REQUEST_HOST") .   $link  ;
        $mailtext  =  "Banner: " . date( "d.m H:i " , $banner->getStarttime()) . " - " . date( "d.m H:i " , $banner->getEndtime()) .  "<br>\n" ;
        $mailtext  .= "Status: " . $messageSubject . "<br>\n" ;
        $invitationText = $this->getInviteText() ;
        if ( $invitationText ) {
            $mailtext .= "<br>\n<b>Invitation: " . $invitationText . "wurde verschickt /was sent!</b><br>\n<br>\n" ;
        }
        $mailtext  .= "Event: " . $event->getName() . "<br>\n" ;
        $mailtext  .= "Image from: " . $imageFrom . "<br>\n" ;
        $mailtext  .= "Assert : " .  ($assetDataNew['uid'] ?? 0 ) . " => uid_local: " . $assetData['uid_local'] .  "<br>\n" ;
        $mailtext .=  "Text: "  . $event->getTeaser() . "<br>\n"  . $banner->getHtml() . "<br>\n" . "<br>\n";
        $mailtext .=  "Link: <a href=\""  .$link. "\"> " . $link . "</a><br>\n" ;

        if ( $banner->getPid() == 56 ) {
            $linkToBanner =  $this->uriBuilder->reset()->setTargetPageUid( 1 )->build() ;
        } else {
            $linkToBanner =  $this->uriBuilder->reset()->setTargetPageUid( 131 )->build() ;
        }

        $linkToBanner = GeneralUtility::getIndpEnv("TYPO3_REQUEST_HOST") . $linkToBanner ;

        $mailtext .=  "Organizer: " . $event->getOrganizer()->getName() .  " " . $event->getOrganizer()->getEmail() . "<br>\n" . "<br>\n";
        $mailtext .=  "Shown/max Views: "  .$banner->getImpressions() . "/"  .$banner->getImpressionsMax() . " | Clicked/max Clicks: "  .$banner->getClicks() . "/"  .$banner->getClicksMax() . "<br>\n" ;
        $mailtext .=  "<br>\n" ;
        $mailtext .=   "Banner Id " .  $banner->getUid() . " will be visible on page: <a href=\"" . $linkToBanner . "\">" .  $linkPageName . "</a><br>\n" ;

        $fromEmail = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'] ;
        $mail = new MailMessage();
        $mail->setFrom($fromEmail);

        $mail->html($mailtext) ;
        $mail->text(strip_tags( $mailtext)) ;

        if( GeneralUtility::validEmail($event->getOrganizer()->getEmail())) {
            $mail->setSubject("[Banner] " .$invitationText . $messageSubject . date( "d.m H:i " , $banner->getStarttime()) . " - " . date( "d.m H:i " , $banner->getEndtime()) ) ;

            $mail->setTo($event->getOrganizer()->getEmail()) ;
            $mail->setCc($fromEmail);
        } else {
            $mail->setSubject("[Banner-NoMailTo] " . $invitationText . $messageSubject . date( "d.m H:i " , $banner->getStarttime()) . " - " . date( "d.m H:i " , $banner->getEndtime()) ) ;

            $mail->setTo($fromEmail);
        }
        $mail->send() ;

        return $this->redirect("show" , "Event" , "JvEvents", ['event' => $event->getUid() , 'showBannerDate' => $bannerDate] , $returnUid) ;
    }

    /**
     * action edit
     *
     * @param Connector $connector
     * @IgnoreValidation("connector")
     * @return void
     */
    public function editAction(Connector $connector): ResponseInterface
    {
       // $this->view->assign('connector', $connector);
       return $this->htmlResponse();
    }

    /**
     * action update
     *
     * @param Connector $connector
     */
    public function updateAction(Connector $connector)
    {
        $this->addFlashMessage('The object NOT was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', AbstractMessage::WARNING);
      //  $this->connectorRepository->update($connector);
        return $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param Connector $connector
     */
    public function deleteAction(Connector $connector): ResponseInterface
    {
        $this->addFlashMessage('The object was NOT deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', AbstractMessage::WARNING);
     //   $this->connectorRepository->remove($connector);
        return $this->redirect('list');
    }

    /**
     * action disable
     *
     */
    public function disableAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * action enable
     *
     */
    public function enableAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * action reducePoints
     *
     */
    public function reducePointsAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * action addPoints
     *
     */
    public function addPointsAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }


    /**
     * @param Organizer|LazyLoadingProxy $organizer
     * @return bool
     */
    private function hasUserAccess( $organizer ) {
        if(! is_object( $organizer ) ) {
            return false ;
        }
        $frontendUser = $this->request->getAttribute('frontend.user');
        if(! is_object( $frontendUser ) || !is_array($frontendUser->user)  ) {
            return false ;
        }

        $feuserUid = (int)$frontendUser->user['uid']  ;
        $users = GeneralUtility::trimExplode("," , $organizer->getAccessUsers() , TRUE ) ;
        if( in_array( $feuserUid  , $users )) {
            return true  ;
        } else {
            $groups = GeneralUtility::trimExplode("," , $organizer->getAccessGroups() , TRUE ) ;
            $feuserGroups = GeneralUtility::trimExplode("," ,  $frontendUser->user['usergroup']  , TRUE ) ;
            foreach( $groups as $group ) {
                if( in_array( $group  , $feuserGroups )) {
                    return true  ;
                }
            }
        }
        return false  ;
    }

    private function getInviteText() {
        $frontendUser = $this->request->getAttribute('frontend.user');
        if(! is_object( $frontendUser ) || !is_array($frontendUser->user)  ) {
            return '' ;
        }

        $feuserGroups = GeneralUtility::trimExplode("," ,  $frontendUser->user['usergroup']  , TRUE ) ;
        // admin User schaltet selber Banner - keine Invitation
        if( in_array( 4  , $feuserGroups )) {
            return ""  ;
        }
        if( in_array( 20  , $feuserGroups )) {
            // Gruppe: banner2allowed
            return " [Jörg + 1] "  ;
        }
        if( in_array( 18  , $feuserGroups )) {
            // Gruppe: bannerallowed
            return " [Jörg] "  ;
        }
    }

}
