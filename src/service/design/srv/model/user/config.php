<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * <note>
 * 1.type为'bbs''user''space''other''api'之一
 * 2.refresh 是否需要数据更新
 * 3.sign为自定义标签 array('标签', '名称','字段')
 * 4.standardSign指定的格式为array('列表标题','url','来源ID','简介')
 * 5.special里的属性可以注释，但不能修改
 * 6.normal为自定义 的设置属性 array('表单类型','标题','说明','多选值(如为变量名可选为以array还是html显示)','其它属性(类型long为长表单,short为短表单,multiple可多选)')
 * 7.表单类型为text  select   radio checkbox textarea 中一种
 * </note>
 * 
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: config.php 23959 2013-01-17 08:36:09Z gao.wanggao $
 * @package src.service.design.srv.model.user
 */
return array(
	'model' => 'user', 
	'name' => '用户', 
	'type' => 'user', 
	'refresh' => true, 
	'sign' => array(
		array('{uid}', '用户ID', 'uid'), 
		array('{username}', '用户名字', 'username'), 
		array('{url}', '空间链接', 'url'), 
		array('{avatar_s}', '用户小头像(50*50)', 'smallavatar'), 
		array('{avatar_m}', '用户中头像(120*120)', 'middleavatar'),
		array('{avatar_b}', '用户大头像(200*200)', 'bigavatar'),
		array('{regdate}', '注册时间', 'regdate'),
		array('{lastvisit}', '访问时间', 'lastvisit'),
		array('{posts}', '发帖数', 'posts'),
		array('{digests}', '精华帖数', 'digests'),
		array('{compositePoint}', '综合积分数', 'compositePoint'),
		array('{realname}', '真实姓名', 'realname'),
		array('{sex}', '性别', 'sex'),
		array('{birthYear}', '出生年份', 'birthYear'),
		array('{birthMonth}', '出生月份', 'birthMonth'),
		array('{birthDay}', '出生日期', 'birthDay'),
		array('{locate_province}', '居住省份', 'locate_province'),
		array('{locate_city}', '居住地', 'locate_city'),
		array('{locate_area}', '居住县', 'locate_area'),
		array('{home_province}', '出生省份', 'home_province'),
		array('{home_city}', '出生地', 'home_city'),
		array('{home_area}', '出生县', 'home_area'),
		array('{homepage}', '个人主页', 'homepage'),
		array('{profile}', '个人简介', 'profile'),
		array('{alipay}', '支付宝', 'alipay'),
		array('{mobile}', '手机号码', 'mobile'),
		array('{telphone}', '电话号码', 'telphone'),
		array('{address}', '邮寄地址', 'address'),
		array('{zipcode}', '邮编', 'zipcode'),
		array('{email}', '邮箱', 'email'),
		array('{aliww}', '阿里旺旺', 'aliww'),
		array('{QQ}', 'QQ', 'qq'),
		array('{MSN}', 'MSN', 'msn'),
		
	),
	 
	'standardSign' => array(
		'sTitle'  => '{username}', 
		'sUrl'    => '{url}', 
		'sFromId' => '{uid}', 
		'sIntro'  => '{profile}',
	), 
	
	//以下为查询及显示条件
	'special' => array(
		'limit'	  =>array('text', '显示条数', '默认10条', '', 'short'),
		'timefmt' => array('select', '时间格式', '', array('m-d' => '04-26', 'Y-m-d' => '2012-04-2', 'Y-m-d H:i:s' => '2012-04-26 11:30', 'H:i:s' => '11:30:59','n月j日'=>'4月26日', 'y年n月j日'=>'12年4月26日', 'auto' => '几天前')), 
		'isblank' => array('radio', '链接打开方式', '', array('0' => '当前窗口', '1' => '新窗口'), ''),
	), 
	
	'normal' => array(
		'usernames' => array('text', '用户名', '多个用户名之间采用空格隔开', '', 'long'), 
		'gid' => array('select', '用户组', '', 'gidOptions|html', ''), 
		'gender' => array('checkbox', '性别', '', array('0' => '男性', '1' => '女性')), 
		'hometown' => array('html', '家乡', '', '', 'user_area|hometown'),
		'location' => array('html', '居住地', '', '', 'user_area|location'),
		'orderby' => array(
			'select',
			'用户排序方式',
			'',
			array(
				'1' => '系统推荐排序',
				'2' => '按主题数倒序',
				'3' => '按发帖时间倒序',
				/*'4' => '按被喜欢数倒序',*/
				'5' => '按注册时间倒序',
				'6' => '按访问时间倒序',
			),
		),
	),
);
?>