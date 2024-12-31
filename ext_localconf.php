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
            ]
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
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    connector {
                        iconIdentifier = jv_banners-plugin-connector
                        title = LLL:EXT:jv_banners/Resources/Private/Language/locallang_db.xlf:tx_jvbanners_plugin.name
                        description = LLL:EXT:jv_banners/Resources/Private/Language/locallang_db.xlf:tx_jv_banners_connector.description
                        tt_content_defValues {
                            CType = list
                            list_type = jvbanners_connector
                        }
                    }
                    List {
                        iconIdentifier = jv_banners-plugin-connector
                        title = LLL:EXT:jv_banners/Resources/Private/Language/locallang_db.xlf:tx_jvbanners_plugin.nameList
                        description = LLL:EXT:jv_banners/Resources/Private/Language/locallang_db.xlf:tx_jv_banners_connector.descriptionList
                        tt_content_defValues {
                            CType = list
                            list_type = jvbanners_list
                        }
                    }
                }
                show = *
            }
       }'
    );
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
			$iconRegistry->registerIcon(
				'jv_banners-plugin-connector',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:jv_banners/Resources/Public/Icons/user_plugin_connector.svg']
			);
		
    }
);
