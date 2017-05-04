<?php

$EM_CONF[$_EXTKEY] = array(
    'title'            => 'Scrpt Style Push',
    'description'      => 'TYPO3 Extension to push javascript and css files over a http/2 connection.',
    'category'         => 'fe',
    'contraints'       => array(
        'depends'   => array(
            'typo3' => '7.6.0-7.6.99',
        ),
        'conflicts' => array(),
    ),
    'state'            => 'beta',
    'uploadfolder'     => false,
    'createDirs'       => '',
    'clearCacheOnLoad' => true,
    'author'           => 'Tim Schreiner',
    'author_email'     => 'schreiner.tim@gmail.com',
    'author_company'   => '',
    'version'          => '1.0.2'
);