<?php

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
