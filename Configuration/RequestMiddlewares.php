<?php

/*
 * This file is part of the "script_style_push" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

return [
    'frontend' => [
        'codemonkey1988/script-style-push/add-link-header' => [
            'target' => \Codemonkey1988\ScriptStylePush\Middleware\AddLinkHeader::class,
            'after' => [
                'typo3/cms-frontend/content-length-headers'
            ]
        ],
    ],
];
