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
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['jvbanners_connector'] = 'pi_flexform';
ExtensionManagementUtility::addPiFlexFormValue('jvbanners_connector', 'FILE:EXT:jv_banners/Configuration/FlexForms/flexform.xml');
