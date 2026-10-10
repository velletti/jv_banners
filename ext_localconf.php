<?php
defined('TYPO3') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'JvBanners',
            'Connector',
            [
                \JVelletti\JvBanners\Controller\ConnectorController::class => 'dummy,list,new, create, list, disable, delete, enable, edit, update, reducePoints, addPoints'
            ],
            // non-cacheable actions
            [
                \JVelletti\JvBanners\Controller\ConnectorController::class => 'new, create, list, disable, delete, enable, edit, update, reducePoints, addPoints'
            ],
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'JvBanners',
            'List',
            [
                \JVelletti\JvBanners\Controller\ConnectorController::class => 'list'
            ],
            // non-cacheable actions
            [
                \JVelletti\JvBanners\Controller\ConnectorController::class => ''
            ],
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
    }
);
