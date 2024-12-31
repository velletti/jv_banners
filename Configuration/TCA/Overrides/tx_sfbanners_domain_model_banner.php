<?php
defined('TYPO3') or die();

$GLOBALS['TCA']['tx_sfbanners_domain_model_banner']['ctrl']['sortby']='crdate DESC' ;
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
$GLOBALS['TCA']['tx_sfbanners_domain_model_banner']['columns']['organizer'] =
    [
        'exclude' => 1,
        'label' => 'Organizer ID',
        'config' => [
            'type' => 'group',
            'allowed' => 'jv_events_domain_model_organizer',
            'size' => 1,
            'eval' => 'int',
            'maxitems' => 1,
            'suggestOptions' => array(
                'default' => array(
                    "additionalSearchFields" => "name,email,uid" ,
                ) ,
            ) ,
        ]
    ] ;
$GLOBALS['TCA']['tx_sfbanners_domain_model_banner']['columns']['fe_user'] =
    [
        'exclude' => 1,
        'label' => 'Frontend User ID',
        'config' => [
            'type' => 'group',
            'allowed' => 'fe_user',
            'size' => 1,
            'eval' => 'int',
            'maxitems' => 1,
            'suggestOptions' => array(
                'default' => array(
                    "additionalSearchFields" => "username,email,uid" ,
                ) ,
            ) ,
        ]
    ] ;

$GLOBALS['TCA']['tx_sfbanners_domain_model_banner']['types']['0'] =
    [
        'showitem' => 'type, title, description,
                --div--;LLL:EXT:sf_banners/Resources/Private/Language/locallang_db.xlf:tx_sfbanners_domain_model_banner.tabs.image,assets,--palette--;;paletteMargins,link,
                --div--;LLL:EXT:sf_banners/Resources/Private/Language/locallang_db.xlf:tx_sfbanners_domain_model_banner.tabs.html,html,
                --div--;LLL:EXT:sf_banners/Resources/Private/Language/locallang_db.xlf:tx_sfbanners_domain_model_banner.tabs.display, category, excludepages, recursive,
                --div--;LLL:EXT:sf_banners/Resources/Private/Language/locallang_db.xlf:tx_sfbanners_domain_model_banner.tabs.limitations, impressions_max, clicks_max,
                --div--;LLL:EXT:sf_banners/Resources/Private/Language/locallang_db.xlf:tx_sfbanners_domain_model_banner.tabs.statistics, impressions, clicks,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, --palette--;;paletteLanguage,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden,--palette--;;paletteVisibility, fe_group,fe_user,organizer
            ',
    ];

$GLOBALS['TCA']['tx_sfbanners_domain_model_banner']['columns']['description']['config'] = [
    'type' => 'text',
    'cols' => 60,
    'rows' => 10,
    'behaviour' => [
        'allowLanguageSynchronization' => true,
    ]
]
;