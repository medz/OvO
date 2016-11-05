<?php
defined('WEKIT_VERSION') or exit(403);

/**
 * 后台默认菜单配置信息,菜单配置格式如下：
 * 一个菜单个配置格式中包含: 菜单名称, 路由信息, 菜单图标, 菜单tip, 父节点, 上一个菜单
 * 菜单:  'key' => array('菜单名称', '应用路由', 'icon' , ' tip' ,'父节点key', '上一个菜单key'),
 * 
 * <note>
 * 1. 如果没有填写上一个菜单则默认放置在节点最后.
 * 2. 如果没有父节点则并放置在'上一个菜单之后'.
 * 3. 如果'父节点','上一个菜单'都没有则散落的放置在最外层.
 * </note>
 * 
 * 节点定义: 'Key' => array('节点名称', '父节点', 'log描述', 是否在后台积分策略中显示 , 是否使用奖励上限次数功能),
 */
return array(
	
	'global' => array('全局', '', '', true),
	'register' => array('注册', 'global', '注册;积分变化【{$cname}:{$affect}】', true, true),
	'login' => array('登录', 'global', '登录;积分变化【{$cname}:{$affect}】', true),
	'sendmsg' => array('发消息', 'global', '发消息;积分变化【{$cname}:{$affect}】', true),
	'punch' => array('每日打卡', 'global', '每日打卡;积分变化【{$cname}:{$affect}】', false),

	'bbs' => array('论坛', '', '', true),
	'post_topic' => array('发布主题', 'bbs', '{$username}在版块{$forumname}发布主题;积分变化【{$cname}:{$affect}】', true),
	'delete_topic' => array('删除主题', 'bbs', '{$username}发布的主题“{$title}”被管理员{$operator}删除了;积分变化【{$cname}:{$affect}】', true),
	'post_reply' => array('发布回复', 'bbs', '{$username}在版块{$forumname}发布回复;积分变化【{$cname}:{$affect}】', true),
	'delete_reply' => array('删除回复', 'bbs', '{$username}在版块{$forumname}发布回复被管理员{$operator}删除;积分变化【{$cname}:{$affect}】', true),
	'digest_topic' => array('精华主题', 'bbs', '{$username}在版块{$forumname}发布的主题被设为精华主题;积分变化【{$cname}:{$affect}】', true),
	'remove_digest' => array('取消精华', 'bbs', '{$username}在版块{$forumname}发布的主题被取消精华;积分变化【{$cname}:{$affect}】', true),
	'push_thread' => array('帖子推送', 'bbs', '{$username}在版块{$forumname}发布的主题被推送到门户;积分变化【{$cname}:{$affect}】', true),
	'upload_att' => array('上传附件', 'bbs', '{$username}在版块{$forumname}上传附件;积分变化【{$cname}:{$affect}】', true),
	'download_att' => array('下载附件', 'bbs', '{$username}在版块{$forumname}下载附件;积分变化【{$cname}:{$affect}】', true),
	'belike' => array('被喜欢', 'bbs', '内容被{$forumname}喜欢;积分变化【{$cname}:{$affect}】', true),
	'olpay_credit' => array('积分充值', 'bbs', '{$username}使用在线充值功能，充值金额:{$number};积分变化【{$cname}:{$affect}】', false),
	'exchange_out' => array('积分转换(转出)', 'bbs', '{$username}使用积分转换功能;积分变化【{$cname}:{$affect}】', false),
	'exchange_in' => array('积分转换(转入)', 'bbs', '{$username}使用积分转换功能;积分变化【{$cname}:{$affect}】', false),
	'transfer_in' => array('积分转帐(转入)', 'bbs', '{$fromusername}使用积分转帐功能;转给{$username}积分;积分变化【{$cname}:{$affect}】', false),
	'transfer_out' => array('积分转帐(转出)', 'bbs', '{$username}使用积分转换功能;转给{$tousername}积分;积分变化【{$cname}:{$affect}】', false),
	'buythread' => array('购买帖子', 'bbs', '{$username}购买了帖子“{$title}”的查看权限;积分变化【{$cname}:{$affect}】', false),
	'sellthread' => array('出售帖子', 'bbs', '{$username}的帖子“{$title}”成功出售;积分变化【{$cname}:{$affect}】', false),
	'attach_buy' => array('购买附件', 'bbs', '{$username}购买了附件“{$name}”;积分变化【{$cname}:{$affect}】', false),
	'attach_sell' => array('出售附件', 'bbs', '{$username}的附件“{$name}”成功出售;积分变化【{$cname}:{$affect}】', false),
	'task_reward' => array('完成任务奖励', 'global', '{$username}完成任务{$taskname}获得奖励;积分变化【{$cname}:{$affect}】', false),
	'invite_reward' => array('成功邀请好友', 'global', '成功邀请好友{$friend}入驻;积分变化【{$cname}:{$affect}】', false),
	'admin_set' => array('后台用户设置积分', 'global', '后台用户{$username}重置用户积分;积分变化：【{$cname}:{$affect}】', false),
	'app' => array('应用', '', '', true),
	'app_default' => array('在线应用', 'app', '{$appname};积分变化：【{$cname}:{$affect}】', false),
);
/**=====配置结束于此=====**/
