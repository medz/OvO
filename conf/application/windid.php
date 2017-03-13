<?php

defined('WEKIT_VERSION') or exit(403);
/*
 * windid
 */

return [
    'directory'  => '../conf/windid/directory.php',
    'components' => ['resource' => 'CONF:windid.components.php'],

    'web-apps' => [
        'windid' => [
            'root-path' => 'APPS:windid',
            'modules'   => [
                'pattern' => [
                    'controller-path' => 'APPS:{m}.controller',
                    'template-path'   => 'TPL:{m}',
                    'compile-path'    => 'DATA:compile.template',
                ],
                'default' => [
                    'controller-path'   => 'APPS:windid.controller',
                    'controller-suffix' => 'Controller',
                    'error-handler'     => PwErrorController::class,
                    'template-path'     => 'TPL:windid',
                    'compile-path'      => 'DATA:compile.template',
                ],
            ],
        ],
    ],
];
