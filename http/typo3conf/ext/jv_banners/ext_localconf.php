<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'JVE.JvBanners',
            'Connector',
            [
                'Connector' => 'dummy,new, create, list, disable, delete, enable, edit, update, reducePoints, addPoints'
            ],
            // non-cacheable actions
            [
                'Connector' => 'new, create, list, disable, delete, enable, edit, update, reducePoints, addPoints'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    connector {
                        iconIdentifier = jv_banners-plugin-connector
                        title = LLL:EXT:jv_banners/Resources/Private/Language/locallang_db.xlf:tx_jv_banners_connector.name
                        description = LLL:EXT:jv_banners/Resources/Private/Language/locallang_db.xlf:tx_jv_banners_connector.description
                        tt_content_defValues {
                            CType = list
                            list_type = jvbanners_connector
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
