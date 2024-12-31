<?php
namespace JVelletti\JvBanners\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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
 * The repository for Connectors
 */
class BannerRepository extends \DERHANSEN\SfBanners\Domain\Repository\BannerRepository
{
    protected $defaultOrderings = ['endtime' => QueryInterface::ORDER_DESCENDING];

    public function findActiveFutureBanners(?int $endTime = (86400 *30)): \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array
    {
        $query = $this->createQuery();
        $query->setOrderings(['pid' => QueryInterface::ORDER_ASCENDING],['starttime' => QueryInterface::ORDER_ASCENDING]);
        $query->getQuerySettings()->setRespectStoragePage(false)->setIgnoreEnableFields(true);
        $query->matching(
            $query->logicalAnd(
                $query->equals('hidden', 0),
                $query->equals('deleted', 0),
                $query->greaterThanOrEqual('endtime', time()),
                $query->lessThanOrEqual('starttime',  $endTime)
            )
        );
        // $this->showSql($query , __FILE__ , __LINE__ ) ;
        return $query->execute();
    }



    /**
     * @param QueryInterface $query
     */
    public function showSql($query , $file , $line ) {
        $queryParser = GeneralUtility::makeInstance(Typo3DbQueryParser::class);

        $sqlquery = $queryParser->convertQueryToDoctrineQueryBuilder($query)->getSQL() ;
        echo "<html><body><h2>See File" . $file  . " Line :" . $line ." </h2><div>";
        echo $sqlquery ;
        echo "<hr>Values: <br>" ;
        $values = ($queryParser->convertQueryToDoctrineQueryBuilder($query)->getParameters()) ;
        echo "<pre>" ;
        echo var_export($values , true ) ;
        echo "</pre>" ;
        $from = [] ;
        $to = [] ;
        foreach (array_reverse( $values ) as $key => $value) {
            $from[] = ":" .$key ;
            $to[] = $value ;
        }
        $sqlFinalQuery = str_replace($from , $to , (string) $sqlquery ) ;
        echo "<hr>Final: <br>" ;
        echo str_replace( ["(", ")"]  , ["<br>(", ")<br>"] , $sqlFinalQuery ) ;
        $result = $query->execute() ;
        echo "<br><hr>Anzahl: " .  $result->count() ;

        echo "<hr><br></div></body></html>" ;
        die;
    }
}
