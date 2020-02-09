<?php
namespace JVE\JvBanners\Utility;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AssetUtility{


	public static function loadSysFileReference( $uid_foreign,  $table , $field, $lng=-3 ) {

        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance( "TYPO3\\CMS\\Core\\Database\\ConnectionPool");

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $connectionPool->getConnectionForTable('sys_file_reference')->createQueryBuilder();
        $queryBuilder->select('*')
            ->from('sys_file_reference') ;

        $expr = $queryBuilder->expr();
        $queryBuilder->where(
            $expr->eq('uid_foreign', $queryBuilder->createNamedParameter($uid_foreign, Connection::PARAM_INT))
        )->andWhere(
            $expr->eq('tablenames', $queryBuilder->createNamedParameter($table, Connection::PARAM_STR))
        )->andWhere(
            $expr->eq('fieldname', $queryBuilder->createNamedParameter($field, Connection::PARAM_STR))
        ) ;

        $response = $queryBuilder->execute()->fetch();



		return $response;

	}

    public static function generateAssetfromSysFileReference(   $table , $field, $assetData ,$tableLocal = 'sys_file' , $link = false ) {


        /** @var \JVE\JvMediaConnector\Domain\Model\FileReference $asset */
        $asset = GeneralUtility::makeInstance( "JVE\\JvMediaConnector\\Domain\\Model\\FileReference");
        $asset->setTablenames($table) ;
        $asset->setTableLocal($tableLocal) ;
        $asset->setFieldname($field) ;
        $asset->setUidLocal($assetData['uid_local']) ;
       // $asset->setUidForeign($assetData['uid_foreign']) ;
        if( $link ) {
            $asset->setLink( $link );
        }

		return $asset;

	}



}