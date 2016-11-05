<?php

/**
 * 门户 - 友情链接 - 配置
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
return array(
    'model' => 'link',
    'name' => '友情链接',
    'type' => 'other',
    'refresh' => true,
    'sign' => array(
        array('{lid}', '友情链接ID', 'lid'),
        array('{name}', '友情链接名称', 'name'),
        array('{url}', '访问地址', 'url'),
        array('{logo}', '友情链接logo', 'logo'),

    ),
    'standardSign' => array('sTitle' => '{name}', 'sUrl' => '{url}', 'sFromId' => '{lid}', 'sIntro' => ''),
    'special' => array(
        'titlenum' => array('text', '标题长度', '0为不限制', '', 'short'),
        'limit' => array('text', '显示条数', '默认10条', '', 'short'),
        'isblank' => array('radio', '链接打开方式', '', array('0' => '当前窗口', '1' => '新窗口'), ''),
    ),

    'normal' => array(
        'linkType' => array('select', '友情链接分类', '', 'linkType|array'),
        'isLog' => array('select', '是否带logo', '', array(-1 => '所有', 1 => '有logo', 0 => '无logo')),
    ),
);
