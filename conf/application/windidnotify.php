<?php

defined('WEKIT_VERSION') or exit(403);

return [
    'web-apps' => [
        'windidnotify' => [
            'root-path' => 'APPS:windidnotify',
            'modules'   => [
                'default' => [
                    'controller-path'   => 'APPS:windidnotify.controller',
                    'controller-suffix' => 'Controller',
                    'template-path'     => 'TPL:windidnotify',
                    'compile-path'      => 'DATA:compile.template.windidnotify',
                ],
            ],
        ],
    ],
];
