<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>.
 *
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
return [
    'model'   => 'tag',
    'name'    => '话题',
    'type'    => 'bbs',
    'refresh' => true,
    'sign'    => [
        ['{tagid}', '话题ID', 'tag_id'],
        ['{title}', '话题名称', 'tag_name'],
        ['{url}', '话题访问地址', 'url'],
        ['{logo}', '话题封面', 'logo'],
        ['{attention_count}', '关注数', 'attention_count'],
        ['{content_count}', '内容关联数', 'content_count'],
        ['{excerpt}', '话题摘要', 'excerpt'],
        ['{thumb|width|height}', '缩略图片｜宽｜高', 'thumb_attach'],
    ],
    'standardSign' => ['sTitle' => '{title}', 'sUrl' => '{url}', 'sFromId' => '{tagid}', 'sIntro' => ''],
    'special'      => [
        'titlenum' => ['text', '标题长度', '0为不限制', '', 'short'],
        'limit'    => ['text', '显示条数', '默认10条', '', 'short'],
        'isblank'  => ['radio', '链接打开方式', '', ['0' => '当前窗口', '1' => '新窗口'], ''],
    ],

    'normal' => [
        'tag_ids'     => ['text', '话题ID', '多个话题之间采用空格隔开', '', 'long'],
        'category_id' => ['select', '话题分类', '', 'categorys|array', ''],
        'islogo'      => ['radio', '封面', '', ['0' => '否', '1' => '是']],
        'order'       => ['select', '主题排序方式', '', ['0' => '按热门话题倒序', '1' => '按话题关注人数倒序']],
    ],
];
