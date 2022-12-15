<?php
defined('TYPO3') or die();

$GLOBALS['TCA']['tx_sfbanners_domain_model_banner']['columns']['link']=
       [
            'exclude' => 1,
            'label' => 'Linked to event ID',
            'config' => [
                'type' => 'input',
                'size' => 9,
                'eval' => 'int',
            ]
        ] ;
$GLOBALS['TCA']['tx_sfbanners_domain_model_banner']['columns']['hidden']['label'] = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden';
$GLOBALS['TCA']['tx_sfbanners_domain_model_banner']['columns']['starttime']['label'] = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime';
$GLOBALS['TCA']['tx_sfbanners_domain_model_banner']['columns']['endtime']['label'] = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime';
$GLOBALS['TCA']['tx_sfbanners_domain_model_banner']['columns']['sys_language_uid']['label'] = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language';


$GLOBALS['TCA']['tx_sfbanners_domain_model_banner']['palettes' ]['paletteVisibility'] = ['showitem' => 'starttime, endtime,--linebreak__,link'] ;
$GLOBALS['TCA']['tx_sfbanners_domain_model_banner']['types']['0'] =
    [
        'showitem' => 'type, title, description,
			--div--;LLL:EXT:sf_banners/Resources/Private/Language/locallang_db.xlf:tx_sfbanners_domain_model_banner.tabs.image,assets,--palette--;;paletteMargins,
			--div--;LLL:EXT:sf_banners/Resources/Private/Language/locallang_db.xlf:tx_sfbanners_domain_model_banner.tabs.display, category, excludepages, recursive,
			--div--;LLL:EXT:sf_banners/Resources/Private/Language/locallang_db.xlf:tx_sfbanners_domain_model_banner.tabs.limitations, impressions_max, clicks_max,
			--div--;LLL:EXT:sf_banners/Resources/Private/Language/locallang_db.xlf:tx_sfbanners_domain_model_banner.tabs.statistics, impressions, clicks,
			--div--;LLL:EXT:sf_banners/Resources/Private/Language/locallang_db.xlf:tx_sfbanners_domain_model_banner.tabs.language, --palette--;;paletteLanguage,
			--div--;Access, hidden,--palette--;;paletteVisibility,
		'
    ];
