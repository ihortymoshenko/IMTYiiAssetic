<?php

/*
 * This file is part of the IMTYiiAssetic package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array(
    'aliases'       => array(
        'IMT\YiiAssetic' => __DIR__ . '/../../../src/',
    ),
    'basePath'      => __DIR__ . '/..',
    'components'    => array(
        'assetManager' => array(
            'class'    => 'IMT\YiiAssetic\AssetManager',
            'basePath' => __DIR__ . '/../web/assets',
            'baseUrl'  => '/assets',
        ),
        'clientScript' => array(
            'class'    => 'IMT\YiiAssetic\ClientScript',
            'packages' => array(
                'smth' => array(
                    'basePath' => 'application.web',
                    'js'       => array(
                        'js/file.js',
                        'js/file2.js',
                    ),
                    'css'      => array(
                        'css/file.css',
                        'css/file2.css'
                    ),
                    'combineTo' => 'smth',
                ),
            ),
        ),
    ),
);
