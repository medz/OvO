<?php

defined('WEKIT_VERSION') or exit(403);
/*
 * 全局产品级应用	配置
*/
return [
    //'isclosed' => '1',

    'directory'    => '../conf/directory.php',
    'publish'      => ['resource' => 'CONF:publish.php'],
    'global-vars'  => ['resource' => ['CONF:baseconfig.php', 'CONF:optimization.php']],
    'cacheService' => ['resource' => 'CONF:cacheService.php'],
    'components'   => ['resource' => 'CONF:components.php'],

    'web-apps' => [
        'default' => [
            'charset'   => 'utf-8',
            'error-dir' => 'TPL:common.windweb',
        ],
    ],
];
