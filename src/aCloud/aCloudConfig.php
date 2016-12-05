<?php

defined('WEKIT_VERSION') or exit(403);
/*
 * 全局产品级应用配置
 */
return array(
    'components' => array('resource' => 'CONF:components.php'),

    /**=====配置开始于此=====**/
    'web-apps' => array(
        'acloud' => array(
            'charset'   => 'utf-8',
            'root-path' => 'SRC:aCloud',
            'modules'   => array(
                'default' => array(
                    'controller-path' => '',
                    'error-handler'   => '',
                    'template-path'   => '',
                    'compile-path'    => '',
                    'theme-package'   => '',
                ),
            ),
        ),
    ),
);
