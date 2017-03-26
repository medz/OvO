<?php

defined('WEKIT_VERSION') or exit(403);
/*
 * 应用安装配置
 */

return [
    'components' => [
        'windView' => [
            'properties' => [
                'viewResolver' => ['path' => 'WindNormalViewerResolver'], ], ],
        'router' => [], ],

    'web-apps' => [
        'install' => [
            'root-path' => 'APPS:install',
            'modules'   => [
                'default' => [
                    'controller-path'   => 'INSTALL:controller',
                    'controller-suffix' => 'Controller',
                    'template-path'     => 'TPL:install',
                    'compile-path'      => 'DATA:compile.template.install',
                    'error-handler'     => 'INSTALL:controller.MessageController',
                    'theme-package'     => 'THEMES:',
                ],
            ],
        ],
    ],
];
