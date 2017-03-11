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
return array(
    'model'   => 'forum',
    'name'    => '版块',
    'type'    => 'bbs',
    'refresh' => true,
    'sign'    => array(
        array('{fid}', '版块ID', 'fid'),
        array('{forum}', '版块名称', 'name'),
        array('{forumUrl}', '版块Url', 'forum_url'),
        array('{descrip}', '版块简介', 'descrip'),
        array('{logo}', '版块图标', 'logo'),

        array('{thread}', '最后主题', 'lastthread'),
        array('{threadTime}', '最后发布主题时间', 'lastthread_time'),
        array('{threadUser}', '最后发布主题用户', 'lastthread_username'),
        array('{threadSpace}', '最后发布主题用户Url', 'lastthread_space'),
        array('{avatar_s}', '最后发布主题用户小头像(50*50)', 'lastthread_smallavatar'),
        array('{avatar_m}', '最后发布主题用户中头像(120*120)', 'lastthread_middleavatar'),

        array('{postUser}', '最后回复用户', 'lastpost_username'),
        array('{postAvatar_s}', '最后回复小头像(50*50)', 'lastpost_smallavatar'),
        array('{postAvatar_m}', '最后回复中头像(120*120)', 'lastpost_middleavatar'),
        array('{postTime}', '最后回复时间', 'lastpost_time'),
        array('{postSpace}', '最后回复用户Url', 'lastpost_space'),

        //array('{todaythreads}', '今日主题数','todaythreads'),
        array('{todayposts}', '今日发帖数', 'todayposts'),
        array('{threads}', '主题数', 'threads'),
        array('{posts}', '回复数', 'posts'),
        array('{article}', '总帖数', 'article'),

    ),
    'standardSign' => array('sTitle' => '{forum}', 'sUrl' => '{forumUrl}', 'sFromId' => '{fid}', 'sIntro' => '{descrip}'),
    'special'      => array(
        //'titlenum'	=>array('text','名称长度','0为不限制','','short'),
        'desnum'  => array('text', '简介长度', '0为不限制', '', 'short'),
        'limit'   => array('text', '显示条数', '默认10条', '', 'short'),
        'timefmt' => array('select', '时间格式', '', array('m-d' => '04-26', 'Y-m-d' => '2012-04-26', 'Y-m-d h:i:s' => '2012-04-26 11:30', 'H:i:s' => '11:30:59', 'n月j日' => '4月26日', 'y年n月j日' => '12年4月26日', 'auto' => '几天前')),
        'isblank' => array('radio', '链接打开方式', '', array('0' => '当前窗口', '1' => '新窗口'), ''),
    ),

    'normal' => array(
        'fids'  => array('select', '版块', '', 'forumOption|html', 'multiple'),
        'order' => array('select', '版块排序方式', '', array('0' => '按默认顺序', '1' => '按主题数倒序', '2' => '按今日发帖数倒序', '3' => '按总帖数倒序', '4' => '按最后回复')),
    ),
);
