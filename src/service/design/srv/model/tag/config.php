<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package
 */
return array(
    'model' => 'tag',
    'name' => '话题',
    'type' => 'bbs',
    'refresh' => true,
    'sign' => array(
        array('{tagid}', '话题ID', 'tag_id'),
        array('{title}', '话题名称', 'tag_name'),
        array('{url}', '话题访问地址', 'url'),
        array('{logo}', '话题封面', 'logo'),
        array('{attention_count}', '关注数', 'attention_count'),
        array('{content_count}', '内容关联数', 'content_count'),
        array('{excerpt}', '话题摘要', 'excerpt'),
        array('{thumb|width|height}', '缩略图片｜宽｜高', 'thumb_attach'),
    ),
    'standardSign' => array('sTitle' => '{title}', 'sUrl' => '{url}', 'sFromId' => '{tagid}', 'sIntro' => ''),
    'special' => array(
        'titlenum' => array('text', '标题长度', '0为不限制', '', 'short'),
        'limit' => array('text', '显示条数', '默认10条', '', 'short'),
        'isblank' => array('radio', '链接打开方式', '', array('0' => '当前窗口', '1' => '新窗口'), ''),
    ),

    'normal' => array(
        'tag_ids' => array('text', '话题ID', '多个话题之间采用空格隔开', '', 'long'),
        'category_id' => array('select', '话题分类', '', 'categorys|array', ''),
        'islogo' => array('radio', '封面', '', array('0' => '否', '1' => '是')),
        'order' => array('select', '主题排序方式', '', array('0' => '按热门话题倒序', '1' => '按话题关注人数倒序')),
    ),
);
