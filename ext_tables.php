<?php
defined('TYPO3') || die('Access denied.');

call_user_func(
    function()
    {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'JvBanners',
            'Connector',
            'LLL:EXT:jv_banners/Resources/Private/Language/locallang.xlf:tx_jvbanners_plugin.name',
            'jv_banners-plugin-connector',
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'JvBanners',
            'List',
            'LLL:EXT:jv_banners/Resources/Private/Language/locallang.xlf:tx_jvbanners_plugin.nameList',
            'jv_banners-plugin-connector',
        );

        //      $pluginSignature = str_replace('_', '', 'jv_add2group') . '_add2group';
        //      $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        //      \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:jv_add2group/Configuration/FlexForms/flexform.xml');
        //      \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('jv_add2group', 'Configuration/TypoScript', 'Add Usergroup to Frontenduser');

    }
);

