<?php
defined('WEKIT_VERSION') || exit('Forbidden');
/**
 * Enter description here ...
 * 
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Nov 8, 2011
 * @link http://www.phpwind.com
 * @copyright 2011 phpwind.com
 * @license
 * @version $Id: permissions.php 24890 2013-02-25 08:36:04Z jieyin $
 */

/**
 * 权限点模板分为两类
 * * <note>
 * 1. 通用型input、raido、checkbox等　参数说明 array('类型(input|radio|checkbox)'、'权限点名称','权限点描述')
 * 1.1 input类型可以设置第5个元素：作为单位展示
 * 2. 自定义类型html array('html'、'权限点名称','权限点描述') html相关模板片段需在permission_html_segments.htm中定义
 * </note>
 */
return array(
	/* 基本权限 */
	'allow_visit'			=> array('radio', 'basic', '站点访问', '关闭后，用户将不能访问站点的任何页面'),
	'user_binding'			=> array('radio', 'basic', '多账号绑定', '开启后，用户可以进行多账号绑定'),
	'allow_report'			=> array('radio', 'basic', '使用举报', ''),
	//'login_types'			=> array('checkbox', '登录方式', '', array('username'=>'用户名','uid'=>'UID','email'=>'邮箱','mobile'=>'手机')),

	//'max_title_length'	=> array('input', 'basic', '帖子标题最大长度','', '字'),
	//'content_length_range'=> array('html', 'basic', '内容长度控制','', '字'),
	'allow_publish_vedio'	=> array('radio', 'basic', '发视频', ''),
	'allow_publish_music'	=> array('radio', 'basic', '发音乐', ''),
	'multimedia_auto_open'	=> array('checkbox', 'basic', '多媒体自动打开', '', array('flash' => 'flash', 'wmv' => 'wmv', 'rm' => 'rm', 'mp3' => 'mp3')),

	'message_allow_send'	=> array('radio', 'basic', '发送消息', '开启后，用户才有权限发送站内消息'),
	'message_max_send'		=> array('input', 'basic', '每日发送消息条数', '设置用户每天最多发送消息条数，超出限制后不能再发送消息', ''),

	'tag_allow_add'			=> array('radio', 'basic', '添加话题', '开启后，用户可以添加话题'),
	
	'remind_open'			=> array('radio', 'basic', '@提醒功能', ''),
	'remind_max_num'		=> array('input', 'basic', '每次最多能@人数', '0或留空代表不限制 ', ''),

	'invite_allow_buy'		=> array('radio', 'basic', '购买邀请码', '当<a href="' . WindUrlHelper::createUrl('config/regist/run'). '" class="J_linkframe_trigger">【注册登录设置】</a>开启邀请注册后设置生效'),
	'invite_buy_credit_num' => array('input', 'basic', '消耗积分', '0或留空为免费，消耗积分类型，请前往<a href="' . WindUrlHelper::createUrl('config/regist/run'). '" class="J_linkframe_trigger">【注册登录设置】</a>中设置', ''),
	'invite_limit_24h'		=> array('input', 'basic', '24小时购买数量限制', '控制24小时内最多可以购买的邀请码数量', '个'),

	/* 论坛权限 */
	'allow_read'			=> array('radio', 'basic', '浏览帖子', '开启后，用户可以浏览帖子'),
	'allow_post'			=> array('radio', 'basic', '发布主题', ''),
	'allow_reply'			=> array('radio', 'basic', '发布回复', ''),
	'allow_thread_extend'	=> array('checkbox', 'basic', '帖子扩展功能', '', array(/*'anonymous' => '匿名帖', 'html' => 'html', */'sell' => '出售帖', 'hide' => '隐藏帖')),
	'post_check'			=> array('radio', 'basic', '发帖需审核', '', array('0' => '均需审核', '1' => '按版块设置', '2' => '无需审核')),
	'thread_award'			=> array('radio', 'basic', '设置回帖奖励', ''),
	'remote_download'		=> array('radio', 'basic', '下载远程图片', ''),
	'threads_perday'		=> array('input', 'basic', '每日最多发帖', '', ''),
	'thread_edit_time'		=> array('html', 'basic', '编辑控制', '用户发帖成功后，可以在设定的时间段内重新编辑帖子，0或留空表示不限制', ''),
	'post_pertime'			=> array('html', 'basic', '连续发帖时间间隔', '设定的时间间隔内用户不可连续发帖，0或留空表示不限制，此功能原名为：灌水预防', ''),
	'post_modify_time'		=> array('html', 'basic', '帖子编辑记录时间', '超过该时间后，帖子编辑将留下编辑记录', ''),
	'look_thread_log'		=> array('radio', 'basic', '查看帖子操作记录', '', ''),
	'post_url_num'			=> array('input', 'basic', '链接帖发帖数限制', '', ''),

	'allow_upload'			=> array('radio', 'basic', '上传附件权限', '', array('0' => '不允许上传附件', '1' => '允许上传附件，按照版块设置奖励或扣除积分', '2' => '允许上传附件，不奖励或扣除积分'), 'vertical'),
	'allow_download'		=> array('radio', 'basic', '下载附件权限', '', array('0' => '不允许下载附件', '1' => '允许下载附件，按照版块设置奖励或扣除积分', '2' => '允许下载附件，不奖励或扣除积分'), 'vertical'),
	'uploads_perday'		=> array('input', 'basic', '一天最多上传附件个数', '', ''),
	//'upload_file_types'		=> array('html', 'basic', '附件上传的后缀和尺寸', ''),

	'sell_credits'			=> array('html', 'basic', '出售帖允许的积分类型', ''),
	'sell_credit_range'		=> array('html', 'basic', '出售帖允许的积分大小', ''),
	'enhide_credits'		=> array('html', 'basic', '隐藏帖允许的积分类型', ''),
	
	'allow_sign'			=> array('radio', 'basic', '签名功能', ''),
	'sign_max_height'		=> array('input', 'basic', '最大高度控制[像素]', ''),
	'sign_max_length'		=> array('input', 'basic', '内容长度控制[字]', ''),
	'sign_ubb'				=> array('radio', 'basic', '签名Ubb 代码功能', ''),
	'sign_ubb_img'			=> array('radio', 'basic', '签名[img]标签功能', ''),

	'allow_add_vote'		=> array('radio', 'basic', '发布投票', ''),
	'allow_participate_vote'=> array('radio', 'basic', '参与投票', ''),
	'allow_view_vote'		=> array('radio', 'basic', '查看投票人员', ''),

	'reply_locked_threads'	=> array('radio', 'basic', '回复锁定帖', ''),
	'view_ip_address'		=> array('radio', 'basic', '查看用户IP'),

	/*  ============================= 管理权限点 ================================== */

	'force_operate_reason'	=> array('radio', 'system', '强制输入操作理由', ''),
	'manage_level'			=> array('input', 'system', '权限等级', '请输入数字，数字越大权限越高，权限高的用户能够操作权限低的用户<br />涉及范围有：移动、编辑、删除、锁定、压帖、屏蔽、禁止、置顶'),
	'operate_thread'		=> array('html', 'systemforum', '帖子操作', ''),
	
	'fresh_delete'			=> array('radio', 'system', '删除新鲜事', '开启后，用户可以删除单条新鲜事'),

	'tag_allow_edit'		=> array('radio', 'system', '话题编辑','开启后，用户可以编辑话题'),
	'tag_allow_manage'		=> array('radio', 'system', '话题聚合页管理','开启后，用户可以在话题聚合页屏蔽内容，防止不适合的内容在话题聚合页显示'),

	'panel_bbs_manage'		=> array('checkbox', 'system', '论坛管理', '', array('thread_check'=>'帖子审核')),
	'panel_user_manage'		=> array('checkbox', 'system', '用户管理', '', array('user_check'=>'用户审核')),
	'panel_report_manage'	=> array('checkbox', 'system', '举报管理', '', array('report_manage'=>'举报管理')),
	'panel_recycle_manage'	=> array('checkbox', 'system', '回收站', '', array('recycle'=>'回收站')),
	'panel_log_manage'		=> array('checkbox', 'system', '管理日志', '', array('log_manage'=>'管理日志')),
	
	'design_allow_manage'	=> array('html', 'system', '操作权限', '门户设计：对门户有最高管理权限，包括编辑结构、编辑模块权限；<br/>编辑模块：对门户的所有模块有编辑权限；<br/>管理内容：推送的内容可以直接显示，拥有模块显示内容、推送内容和添加内容的管理权限;<br/>推送内容需审核：可推送内容到指定模块，但需要通过审核后才会显示。
	'),


	//demo
	'some_radio_permission'	=> array('radio', 'system', 'name text', 'description text',array('v1'=>'label 1','v2'=>'label 2')),
	'some_checkbox_permission' => array('checkbox', 'system', 'name text', 'description text',array('v1'=>'label 1','v2'=>'label 2')),
	'some_html_permission'	=> array('html', 'system', 'name text', 'description text'),
);