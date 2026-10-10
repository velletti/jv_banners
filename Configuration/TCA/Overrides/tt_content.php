<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!defined ('TYPO3')) die ('Access denied.');
$_EXTKEY = "jv_banners" ;

/*
ExtensionManagementUtility::addPlugin(
    Array('LLL:EXT:jv_add2group/Resources/Private/Language/locallang.xlf:add2group.name',
    'jv_banners_connector',) ,
    'list_type' ,
    'jv_banners'

);
*/

// BOTH Lines are needed to see the Flexform in Backend !!1
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tt_content', '--div--;Configuration,pi_flexform,', 'jvbanners_connector', 'after:subheader');
ExtensionManagementUtility::addPiFlexFormValue('*', 'FILE:EXT:jv_banners/Configuration/FlexForms/flexform.xml', 'jvbanners_connector');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('JvBanners', 'Connector', 'LLL:EXT:jv_banners/Resources/Private/Language/locallang.xlf:tx_jvbanners_plugin.name', 'jv_banners-plugin-connector');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('JvBanners', 'List', 'LLL:EXT:jv_banners/Resources/Private/Language/locallang.xlf:tx_jvbanners_plugin.nameList', 'jv_banners-plugin-connector');
