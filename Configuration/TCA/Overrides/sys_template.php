<?php
defined ('TYPO3')  or die ('Access denied.');
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile('jv_banners','Configuration/TypoScript/', 'Connector between Events and Banner ');