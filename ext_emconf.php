<?php

/*
 * This file is part of the "script_style_push" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

$EM_CONF[$_EXTKEY] = [
    'title'            => 'Script Style Push',
    'description'      => 'TYPO3 Extension to push javascript and css files over a http/2 connection.',
    'category'         => 'fe',
    'constraints'       => [
        'depends'   => [
            'typo3' => '9.5.0-10.4.99'
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
    'version'          => '2.3.3'
];
