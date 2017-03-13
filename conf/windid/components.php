<?php

defined('WEKIT_VERSION') or exit(403);
/*
 * 组件配置文件
 *
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
return [
    'pwWidget' => [
        'path'  => 'LIB:engine.component.PwWidget',
        'scope' => 'singleton',
    ],
    'pwComponent' => [
        'path'   => 'LIB:engine.component.PwComponent',
        'scope'  => 'singleton',
        'config' => ['resource' => 'CONF:pwcomponents.php'],
    ],
    'security' => [
        'path'  => 'WindXxtea',
        'scope' => 'singleton',
    ],
    'windLogger' => [
        'constructor-args' => ['0' => ['value' => 'DATA:log'], '1' => ['value' => '2'], '2' => ['value' => 10000]],
    ],
    'router' => [
        'config' => [
            'routes' => [
                'pw' => [
                    'class'   => 'LIB:route.PwCommonRoute',
                    'default' => true,
                ],
            ],
        ],
    ],
    'windView' => [
        'config' => ['themePackPattern' => '{pack}.{theme}.template'],
    ],
    'template' => [
        'config' => ['resource' => 'CONF:compiler.php'],
    ],
    'i18n' => [
        'config' => ['path' => 'SRC:i18n', 'suffix' => '.lang'],
    ],
    'db' => [
        'config' => ['resource' => 'CONF:windid.database.php'],
    ],
    'windToken' => [
        'path'  => 'LIB:engine.extension.token.PwCsrfToken',
        'scope' => 'singleton',
    ],
    'windCookie' => [
        'path'  => 'WindNormalCookie',
        'scope' => 'singleton',
    ],
    'windiddb' => [
        'path'   => 'WIND:db.WindConnection',
        'scope'  => 'singleton',
        'config' => ['resource' => 'CONF:windid.database.php'],
    ],
    'httptransfer' => [
        'path'  => 'WindHttpSocket',
        'scope' => 'prototype',
    ],
    'storage' => [
        'path'  => 'LIB:storage.PwStorageLocal',
        'scope' => 'singleton',
    ],
    'localStorage' => [
        'path'  => 'LIB:storage.PwStorageLocal',
        'scope' => 'singleton',
    ],
    'fileCache' => [
        'path' => 'LIB:engine.extension.cache.PwFileCache',
// 		'path' => 'WIND:cache.strategy.WindFileCache',
        'scope'  => 'application',
        'config' => [
            'dir'           => 'DATA:cache',    //缓存文件存放的目录,注意可读可写
            'suffix'        => 'txt',    //缓存文件的后缀,默认为txt后缀
            'dir-level'     => '0',    //缓存文件存放目录的子目录长度,默认为0不分子目录
            'security-code' => '',    //继承自AbstractWindCache,安全码配置
            'key-prefix'    => 'pw_',     //继承自AbstractWindCache,缓存key前缀
            'expires'       => '0',    //继承自AbstractWindCache,缓存过期时间配置
        ],
    ],
];
