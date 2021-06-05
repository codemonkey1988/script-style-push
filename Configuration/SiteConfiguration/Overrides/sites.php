<?php

/*
 * This file is part of the "script_style_push" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

$GLOBALS['SiteConfiguration']['site']['columns']['assetsToPush'] = [
    'label' => 'LLL:EXT:script_style_push/Resources/Private/Language/locallang_be.xlf:siteConfiguration.site.assetsToPush.label',
    'description' => 'LLL:EXT:script_style_push/Resources/Private/Language/locallang_be.xlf:siteConfiguration.site.assetsToPush.description',
    'config' => [
        'type' => 'input',
        'eval' => 'trim',
        'placeholder' => 'LLL:EXT:script_style_push/Resources/Private/Language/locallang_be.xlf:siteConfiguration.site.assetsToPush.placeholder',
    ],
];

$GLOBALS['SiteConfiguration']['site']['columns']['excludePattern'] = [
    'label' => 'LLL:EXT:script_style_push/Resources/Private/Language/locallang_be.xlf:siteConfiguration.site.excludePattern.label',
    'description' => 'LLL:EXT:script_style_push/Resources/Private/Language/locallang_be.xlf:siteConfiguration.site.excludePattern.description',
    'config' => [
        'type' => 'input',
        'eval' => 'trim',
        'placeholder' => 'LLL:EXT:script_style_push/Resources/Private/Language/locallang_be.xlf:siteConfiguration.site.excludePattern.placeholder',
    ],
];

$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= ',
    --div--;LLL:EXT:script_style_push/Resources/Private/Language/locallang_be.xlf:siteConfiguration.site.tab.pushAssets,
        assetsToPush,
        excludePattern
';
