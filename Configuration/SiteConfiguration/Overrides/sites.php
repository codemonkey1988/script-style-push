<?php

$GLOBALS['SiteConfiguration']['site']['columns']['assetsToPush'] = [
    'label' => 'LLL:EXT:script_style_push/Resources/Private/Language/locallang_be.xlf:siteConfiguration.site.assetsToPush.label',
    'description' => 'LLL:EXT:script_style_push/Resources/Private/Language/locallang_be.xlf:siteConfiguration.site.assetsToPush.description',
    'config' => [
        'type' => 'input',
        'eval' => 'trim',
        'placeholder' => 'LLL:EXT:script_style_push/Resources/Private/Language/locallang_be.xlf:siteConfiguration.site.assetsToPush.placeholder',
    ],
];

$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= ',
    --div--;LLL:EXT:script_style_push/Resources/Private/Language/locallang_be.xlf:siteConfiguration.site.tab.pushAssets,
        assetsToPush
';
