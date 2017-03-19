<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * <note>
 * 1.type为'bbs''user''space''other''api'之一
 * 2.refresh 是否需要设置数据更新
 * 3.sign为自定义标签 array('标签', '名称','字段')
 * 4.standardSign指定的格式为array('列表标题','url','来源ID','简介')
 * 5.special里的属性可以注释，但不能修改
 * 6.normal为自定义 的设置属性 array('表单类型','标题','说明','多选值(如为变量名可选为以array还是html显示)','其它属性(类型long为长表单,short为短表单,multiple可多选)')
 * 7.表单类型为text,select,radio,checkbox,textarea,html(自定义html)  中一种.
 *
 * 8.自定义html的实现：1)'name'=>array('html','标题','说明','','template|key1')，template为模版文件名,key1为勾子名
 * 					2)在template\design\property\目录，新建模板文件, 定义'key1'的模板勾子
 * 					3)<input name="property[abc]" value="{$property[abc]}">',表单name必须以property命名
 * </note>
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: config.php 25436 2013-03-15 08:45:34Z gao.wanggao $
 */
return [
    'model'        => 'thread',
    'name'         => '帖子',
    'type'         => 'bbs',
    'refresh'      => true,
    'standardSign' => ['sTitle' => '{title}', 'sUrl' => '{url}', 'sFromId' => '{tid}', 'sIntro' => '{intro}'],
    'special'      => [
        'titlenum' => ['text', '标题长度', '0为不限制', '', 'short'],
        'desnum'   => ['text', '内容长度', '0为不限制', '', 'short'],
        'limit'    => ['text', '显示条数', '默认10条', '', 'short'],
        'timefmt'  => ['select', '时间格式', '', ['m-d' => '04-26', 'Y-m-d' => '2012-04-26', 'Y-m-d H:i:s' => '2012-04-26 11:30', 'H:i:s' => '11:30:59', 'n月j日' => '4月26日', 'y年n月j日' => '12年4月26日', 'auto' => '几天前']],
        'isblank'  => ['radio', '链接打开方式', '', ['0' => '当前窗口', '1' => '新窗口'], ''],
    ],

    'sign' => [
        ['{tid}', '帖子ID', 'tid'],
        ['{title}', '帖子标题', 'subject'],
        ['{url}', '访问地址', 'url'],
        ['{author}', '楼主', 'created_username'],
        ['{uid}', '楼主UID', 'created_userid'],
        ['{space}', '楼主Url', 'created_space'],
// 		array('{avatar_n}', '楼主迷你头像(50*50)','created_miniavatar'),
        ['{avatar_s}', '楼主小头像(50*50)', 'created_smallavatar'],
        ['{avatar_m}', '楼主中头像(120*120)', 'created_middleavatar'],
        ['{avatar_b}', '楼主大头像(200*200)', 'created_bigavatar'],
        ['{threadTime}', '发帖时间', 'created_time'],
        ['{lastpostUser}', '最后回复用户', 'lastpost_username'],
        ['{lastpostUid}', '最后回复用户UID', 'lastpost_userid'],
        ['{lastpostAvatar_s}', '最后回复小头像(50*50)', 'lastpost_smallavatar'],
        ['{lastpostAvatar_m}', '最后回复中头像(120*120)', 'lastpost_middleavatar'],
        ['{lastpostTime}', '最后回复时间', 'lastpost_time'],
        ['{lastpostSpace}', '最后回复用户Url', 'lastpost_space'],

        ['{forum}', '版块名称', 'forum_name'],
        ['{fid}', '版块ID', 'fid'],
        ['{forumUrl}', '版块Url', 'forum_url'],
        ['{intro}', '帖子内容', 'content'],
        ['{thumb|width|height}', '缩略图片｜宽｜高 （0为自适应）', 'thumb_attach'],
        //array('{markAttach}', '水印图片','mark_attach'),
        ['{replies}', '回复数', 'replies'],
        ['{hits}', '浏览数', 'hits'],
        ['{like}', '喜欢数', 'like_count'],
        ['{tType}', '主题分类', 'tType'],
        ['{tTypeUrl}', '主题分类链接', 'tType_url'],

    ],

    'normal' => [
        'tids'      => ['text', '帖子tid', '多个tid之间采用空格隔开', '', 'long'],
        'usernames' => ['text', '用户名', '多个用户名之间采用空格隔开', '', 'long'],
        'keywords'  => ['text', '标题关键字', '', '', 'long'],
        'fids'      => ['select', '版块', '', 'forumOption|html', 'multiple'],
        'special'   => ['checkbox', '主题类型', '', 'specileType|array'],
        'istop'     => ['checkbox', '置顶', '', ['1' => '本版置顶', '2' => '分类置顶', '3' => '全局置顶']],
        'isdigest'  => ['radio', '精华', '', ['0' => '不限', '1' => '是']],
        'ispic'     => ['radio', '图片', '', ['0' => '不限', '1' => '是']],
        //'isattach'	=>array('radio','附件','',array('0'=>'不限','1'=>'是')),
        //'ismusic'	=>array('radio','音乐','',array('0'=>'否','1'=>'是')),
        //'isvideo'	=>array('radio','视频','',array('0'=>'否','1'=>'是')),
        //'isflash'	=>array('radio','flash','',array('0'=>'否','1'=>'是')),
        'createdtime' => ['select', '主题发布时间', '', ['0' => '不限制', '3600' => '1小时以内', '86400' => '1天以内', '604800' => '1周以内', '2592000' => '1月以内']],
        'posttime'    => ['select', '回复主题时间', '', ['0' => '不限制', '3600' => '1小时以内', '86400' => '1天以内', '604800' => '1周以内', '2592000' => '1月以内']],
        'order'       => ['select', '主题排序方式', '', ['1' => '最新发布时间', '2' => '最新回复时间', '3' => '回复数由多到少', '4' => '浏览数由多到少', '5' => '喜欢数由多到少']],
        'ishighlight' => ['radio', '显示加亮效果', '', ['0' => '否', '1' => '是']],
    ],
];
