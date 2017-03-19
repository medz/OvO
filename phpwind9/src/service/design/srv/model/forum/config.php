<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: config.php 23959 2013-01-17 08:36:09Z gao.wanggao $
 */
return [
    'model'   => 'forum',
    'name'    => '版块',
    'type'    => 'bbs',
    'refresh' => true,
    'sign'    => [
        ['{fid}', '版块ID', 'fid'],
        ['{forum}', '版块名称', 'name'],
        ['{forumUrl}', '版块Url', 'forum_url'],
        ['{descrip}', '版块简介', 'descrip'],
        ['{logo}', '版块图标', 'logo'],

        ['{thread}', '最后主题', 'lastthread'],
        ['{threadTime}', '最后发布主题时间', 'lastthread_time'],
        ['{threadUser}', '最后发布主题用户', 'lastthread_username'],
        ['{threadSpace}', '最后发布主题用户Url', 'lastthread_space'],
        ['{avatar_s}', '最后发布主题用户小头像(50*50)', 'lastthread_smallavatar'],
        ['{avatar_m}', '最后发布主题用户中头像(120*120)', 'lastthread_middleavatar'],

        ['{postUser}', '最后回复用户', 'lastpost_username'],
        ['{postAvatar_s}', '最后回复小头像(50*50)', 'lastpost_smallavatar'],
        ['{postAvatar_m}', '最后回复中头像(120*120)', 'lastpost_middleavatar'],
        ['{postTime}', '最后回复时间', 'lastpost_time'],
        ['{postSpace}', '最后回复用户Url', 'lastpost_space'],

        //array('{todaythreads}', '今日主题数','todaythreads'),
        ['{todayposts}', '今日发帖数', 'todayposts'],
        ['{threads}', '主题数', 'threads'],
        ['{posts}', '回复数', 'posts'],
        ['{article}', '总帖数', 'article'],

    ],
    'standardSign' => ['sTitle' => '{forum}', 'sUrl' => '{forumUrl}', 'sFromId' => '{fid}', 'sIntro' => '{descrip}'],
    'special'      => [
        //'titlenum'	=>array('text','名称长度','0为不限制','','short'),
        'desnum'  => ['text', '简介长度', '0为不限制', '', 'short'],
        'limit'   => ['text', '显示条数', '默认10条', '', 'short'],
        'timefmt' => ['select', '时间格式', '', ['m-d' => '04-26', 'Y-m-d' => '2012-04-26', 'Y-m-d h:i:s' => '2012-04-26 11:30', 'H:i:s' => '11:30:59', 'n月j日' => '4月26日', 'y年n月j日' => '12年4月26日', 'auto' => '几天前']],
        'isblank' => ['radio', '链接打开方式', '', ['0' => '当前窗口', '1' => '新窗口'], ''],
    ],

    'normal' => [
        'fids'  => ['select', '版块', '', 'forumOption|html', 'multiple'],
        'order' => ['select', '版块排序方式', '', ['0' => '按默认顺序', '1' => '按主题数倒序', '2' => '按今日发帖数倒序', '3' => '按总帖数倒序', '4' => '按最后回复']],
    ],
];
