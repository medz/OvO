<?php
/**
 * @author xiaoxia.xu <x_824@sina.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright ©2003-2010 phpwind.com
 * @license
 */
/**
 * 新用户引导配置项
 * order: 排序号
 * open: 是否开启
 * setting: 后台设置地址
 * class: 前台引导页地址
 */
return array(
	'profile' => array(
		'title' => '完善资料',
		'setting' => '',
	 	'guide' => 'guide/interest/run',
	),
	'attention' => array(
		'title' => '推荐关注',
		'setting' => 'admin/u/attentionGuide',
	 	'guide' => 'guide/attention/run',
	),
);