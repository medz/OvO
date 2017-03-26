<?php

/**
 * 门户 - 友情链接 - 配置.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
return [
    'model'   => 'link',
    'name'    => '友情链接',
    'type'    => 'other',
    'refresh' => true,
    'sign'    => [
        ['{lid}', '友情链接ID', 'lid'],
        ['{name}', '友情链接名称', 'name'],
        ['{url}', '访问地址', 'url'],
        ['{logo}', '友情链接logo', 'logo'],

    ],
    'standardSign' => ['sTitle' => '{name}', 'sUrl' => '{url}', 'sFromId' => '{lid}', 'sIntro' => ''],
    'special'      => [
        'titlenum' => ['text', '标题长度', '0为不限制', '', 'short'],
        'limit'    => ['text', '显示条数', '默认10条', '', 'short'],
        'isblank'  => ['radio', '链接打开方式', '', ['0' => '当前窗口', '1' => '新窗口'], ''],
    ],

    'normal' => [
        'linkType' => ['select', '友情链接分类', '', 'linkType|array'],
        'isLog'    => ['select', '是否带logo', '', [-1 => '所有', 1 => '有logo', 0 => '无logo']],
    ],
];
