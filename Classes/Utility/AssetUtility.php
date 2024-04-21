<?php
namespace JVelletti\JvBanners\Utility;

use JVE\JvMediaConnector\Domain\Model\FileReference;
use PDO;
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


        /** @var FileReference $asset */
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


    public static function updateUidLocal($uid , $row ) {

        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance( "TYPO3\\CMS\\Core\\Database\\ConnectionPool");
        try {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = $connectionPool->getConnectionForTable('sys_file_reference')->createQueryBuilder();
            $queryBuilder->update('sys_file_reference')
                ->where( $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter( $uid , PDO::PARAM_INT)) ) ;

            foreach ( $row as $field => $value ) {
                if( $field != "uid") {
                    if ( is_int( $value )) {
                        $queryBuilder->set($field , $value ,false ) ;
                    } else {
                        $queryBuilder->set($field , $value ) ;
                    }
                }
            }
           // var_dump( $queryBuilder->getSQL() );
            // var_dump( $queryBuilder->getParameters() );
            // die;
            $queryBuilder->execute();
        } catch ( \Exception $e) {
            // ignore
            // var_dump($e->getMessage() );
        }
    }

}