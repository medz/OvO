<?php
return array(
	'basic' => array(
		'name' => '基本权限',
		'sub' => array(
			'basic' => array(
				'name' => '基本权限',
				'items' => array(
					'allow_visit','allow_report','view_ip_address'//'user_binding','login_types'
				)
			),
			'publish' => array(
				'name' => '内容发布设置',
				'items' => array(
					/*'max_title_length','content_length_range',*/'allow_publish_vedio',
					'allow_publish_music',//'multimedia_auto_open'
				)
			),
			'message' => array(
				'name' => '消息',
				'items' => array(
					'message_allow_send','message_max_send'
				)
			),
			'tag' => array(
				'name' => '话题',
				'items' => array(
					'tag_allow_add'
				)
			),
			'remind' => array(
				'name' => '@提醒设置',
				'items' => array(
					'remind_open','remind_max_num'
				)
			),
			'invite' => array(
				'name' => '邀请注册',
				'items' => array('invite_allow_buy', 'invite_buy_credit_num', 'invite_limit_24h'),
			),
		)
	),
	'bbs' => array(
		'name' => '论坛权限',
		'sub' => array(
			'thread' => array(
				'name' => '帖子权限',
				'items' => array(
					'allow_read','allow_post','allow_reply','reply_locked_threads','allow_thread_extend',
					'post_check',
					'threads_perday','thread_edit_time','post_pertime',
					'post_modify_time', 'look_thread_log', /*,'post_url_num',*/'allow_upload',
					'allow_download','uploads_perday'/*,'upload_file_types','thread_award','remote_download',*/
				)
			),

			'sellhide' => array(
				'name' => '出售隐藏设置',
				'items' => array(
					'sell_credits','sell_credit_range','enhide_credits'
				)
			),
			'sign' => array(
				'name' => ' 帖子签名设置',
				'items' => array(
					'allow_sign', 'sign_max_height', 'sign_max_length', 'sign_ubb', 'sign_ubb_img'
				)
			),
			'vote' => array(
				'name' => '投票设置',
				'items' => array(
					'allow_add_vote','allow_participate_vote','allow_view_vote'
				)
			),
		)
	),
	'manage_bbs' => array(
		'name' => '论坛权限',
		'manage' => true,
		'sub' => array(
			'bbs' => array(
				'name' => '论坛管理权限',
				'items' => array(
					'manage_level', 'operate_thread', 'force_operate_reason'
				)
			),
			'fresh' => array(
				'name' => '新鲜事管理权限',
				'items' => array('fresh_delete')
			),
			'tag' => array(
				'name' => '话题管理权限',
				'items' => array(
					'tag_allow_edit','tag_allow_manage'
				)
			),
		)
	),
	'manage_design' => array(
		'name' => '门户权限',
		'manage' => true,
		'sub' => array(
			'panel' => array(
				'name' => '门户管理权限',
				'items' => array(
					'design_allow_manage'
				)
			),
		)
	),
	'manage_user' => array(
		'name' => '用户权限',
		'manage' => true,
		'sub' => array(
		)
	),
	'manage_panel' => array(
		'name' => '前台管理',
		'manage' => true,
		'sub' => array(
			'panel' => array(
				'name' => '前台管理权限',
				'items' => array(
					'panel_bbs_manage','panel_user_manage','panel_report_manage','panel_recycle_manage','panel_log_manage'
				)
			),
		)
	),

	'other' => array(
		'name' => '其他权限',
		'sub' => array(
		)
	),
);