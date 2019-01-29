<?php

$EM_CONF[$_EXTKEY] = [
    'title'            => 'Scrpt Style Push',
    'description'      => 'TYPO3 Extension to push javascript and css files over a http/2 connection.',
    'category'         => 'fe',
    'constraints'       => [
        'depends'   => [
            'typo3' => '9.5.0-9.5.99'
        ],
        'conflicts' => [],
        'suggests'  => []
    ],
    'state'            => 'stable',
    'uploadfolder'     => false,
    'createDirs'       => '',
    'clearCacheOnLoad' => true,
    'author'           => 'Tim Schreiner',
    'author_email'     => 'schreiner.tim@gmail.com',
    'author_company'   => '',
    'version'          => '2.0.2'
];
