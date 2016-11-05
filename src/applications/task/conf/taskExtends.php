<?php
/**
 * 该配置文件有两个配置项：
 * reward:配置任务的奖励扩展，目前有两个奖励可设置
 * 注：任务的奖励的扩展需要实现SRC:service.task.srv.reward.PwTaskRewardDoBase的扩展
 *    并且类名采用“PwTaskXXXRewardDo”的格式，XXX为配置的键值，放置在SRC:service.task.srv.reward下
 * 	credit:积分
 *  group:用户组
 *  对应的在SRC:service.task.srv.reward下可以看到PwTaskCreditRewardDo 和PwTaskGroupRewardDo 
 *  注： XXX的第一个字母要大写其他字母和配置保持一致
 *
 * condition：配置任务的完成条件扩展项
 *    完成条件可以根据需求归类，第一类是完成条件的大类（如member,bbs）
 *    每个大类里可以设置对应的小类（如member下的children下设置profile）
 *
 * 每个项可以设置显示在页面上的title: 文本；
 * 同时每个项可以设置一个setting_url：后台请求的扩展的设置
 * 比如credit：settting_url请求我的一个设置页面，我需要设置积分的类型及该积分的数量
 */
return array(
	'reward' => array(
		'credit' => array(
			'title' => '积分',
			'setting_url' => 'task/taskReward/run',
		),
		'group' => array(
			'title' => '用户组',
			'setting_url' => 'task/taskReward/group',
		),
	),
	'condition' => array(
		'member' => array(
			'title' => '会员信息类',
			'children' => array(
				'profile' => array(
					'title' => '完善资料',
					'setting_url' => 'task/taskConditionMember/profile',
				),
				'avatar' => array(
					'title' => '上传头像',
					'setting_url' => 'task/taskConditionMember/avatar',
				),
				'msg' => array(
					'title' => '发送消息',
					'setting_url' => 'task/taskConditionMember/sendMsg',
				),
				'fans' => array(
					'title' => '求粉丝',
					'setting_url' => 'task/taskConditionMember/fans',
				),
				'punch' => array(
					'title' => '打卡签到',
					'setting_url' => 'task/taskConditionMember/punch',
				),
			),
		),
		'bbs' => array(
			'title' => '论坛操作类',
			'children' => array(
				'postThread' => array(
					'title' => '发帖子',
					'setting_url' => 'task/taskConditionBbs/run'
				),
				'reply' => array(
					'title' => '回复帖子',
					'setting_url' => 'task/taskConditionBbs/reply',
				),
				'like' => array(
					'title' => '喜欢帖子',
					'setting_url' => 'task/taskConditionBbs/like',
				),
			),
		),
	),
);