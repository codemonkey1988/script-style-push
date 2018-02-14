<?php

$EM_CONF[$_EXTKEY] = [
    'title'            => 'Scrpt Style Push',
    'description'      => 'TYPO3 Extension to push javascript and css files over a http/2 connection.',
    'category'         => 'fe',
    'contraints'       => [
        'depends'   => [
            'typo3' => '7.6.0-8.7.99'
        ],
        'conflicts' => [],
        'suggests'  => []
    ],
    'state'            => 'beta',
    'uploadfolder'     => false,
    'createDirs'       => '',
    'clearCacheOnLoad' => true,
    'author'           => 'Tim Schreiner',
    'author_email'     => 'schreiner.tim@gmail.com',
    'author_company'   => '',
    'version'          => '1.2.3'
];
