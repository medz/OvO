<?php

defined('WEKIT_VERSION') or exit(403);
/*
 * 全局产品级应用	配置
*/
return [

    /**=====配置开始于此=====**/
    'web-apps' => [
        'phpwind' => [
            'default-module' => 'bbs',
            'root-path'      => 'APPS:bbs',
            'filters'        => [
                'global'  => ['class' => 'APPS:bbs.controller.filter.PwGlobalFilter'],
                'develop' => [
                    'class' => 'APPS:pwadmin.service.srv.filter.PwDebugFilter',
                ],
                'csrf' => [
                    'class'   => PwCsrfTokenFilter::class,
                    'pattern' => '~(bbs/upload/*|windid/uploadAvatar/*|app/upload/run)',
                ],
                'register' => [
                    'class'   => 'APPS:u.controller.filter.UserRegisterFilter',
                    'pattern' => 'u/register/*',
                ],
            ],
            'modules' => [
                'default' => [
                    'controller-path'   => 'APPS:{m}.controller',
                    'controller-suffix' => 'Controller',
                    'error-handler'     => PwErrorController::class,
                    'template-path'     => 'TPL:{m}',
                    'compile-path'      => 'DATA:compile.template',
                    'theme-package'     => 'THEMES:',
                ],
                'admin' => [
                    'controller-path'   => 'APPS:bbs.controller',
                    'controller-suffix' => 'Controller',
                    'error-handler'     => PwErrorController::class,
                    'template-path'     => 'TPL:bbs',
                    'compile-path'      => 'DATA:compile.template.bbs',
                    'theme-package'     => 'THEMES:',
                ],
                'app' => [
                    'controller-path' => 'SRC:extensions.{app}.controller',
                    'template-path'   => 'SRC:extensions.{app}.template',
                    'compile-path'    => 'DATA:compile.template.{app}',
                ],
            ],
        ],
    ],
];
