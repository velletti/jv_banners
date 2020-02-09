<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'JVE.JvBanners',
            'Connector',
            'Banner Connector for Events'
        );

        $pluginSignature = str_replace('_', '', 'jv_banners') . '_connector';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:jv_banners/Configuration/FlexForms/flexform_connector.xml');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('jv_banners', 'Configuration/TypoScript', 'Banner Guthaben');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_jvbanners_domain_model_connector', 'EXT:jv_banners/Resources/Private/Language/locallang_csh_tx_jvbanners_domain_model_connector.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_jvbanners_domain_model_connector');

    }
);
