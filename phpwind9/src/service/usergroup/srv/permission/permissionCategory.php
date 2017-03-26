<?php

return [
    'basic' => [
        'name' => '基本权限',
        'sub'  => [
            'basic' => [
                'name'  => '基本权限',
                'items' => [
                    'allow_visit', 'allow_report', 'view_ip_address', //'user_binding','login_types'
                ],
            ],
            'publish' => [
                'name'  => '内容发布设置',
                'items' => [
                    /*'max_title_length','content_length_range',*/'allow_publish_vedio',
                    'allow_publish_music', //'multimedia_auto_open'
                ],
            ],
            'message' => [
                'name'  => '消息',
                'items' => [
                    'message_allow_send', 'message_max_send',
                ],
            ],
            'tag' => [
                'name'  => '话题',
                'items' => [
                    'tag_allow_add',
                ],
            ],
            'remind' => [
                'name'  => '@提醒设置',
                'items' => [
                    'remind_open', 'remind_max_num',
                ],
            ],
            'invite' => [
                'name'  => '邀请注册',
                'items' => ['invite_allow_buy', 'invite_buy_credit_num', 'invite_limit_24h'],
            ],
        ],
    ],
    'bbs' => [
        'name' => '论坛权限',
        'sub'  => [
            'thread' => [
                'name'  => '帖子权限',
                'items' => [
                    'allow_read', 'allow_post', 'allow_reply', 'reply_locked_threads', 'allow_thread_extend',
                    'post_check',
                    'threads_perday', 'thread_edit_time', 'post_pertime',
                    'post_modify_time', 'look_thread_log', /*,'post_url_num',*/'allow_upload',
                    'allow_download', 'uploads_perday', /*,'upload_file_types','thread_award','remote_download',*/
                ],
            ],

            'sellhide' => [
                'name'  => '出售隐藏设置',
                'items' => [
                    'sell_credits', 'sell_credit_range', 'enhide_credits',
                ],
            ],
            'sign' => [
                'name'  => ' 帖子签名设置',
                'items' => [
                    'allow_sign', 'sign_max_height', 'sign_max_length', 'sign_ubb', 'sign_ubb_img',
                ],
            ],
            'vote' => [
                'name'  => '投票设置',
                'items' => [
                    'allow_add_vote', 'allow_participate_vote', 'allow_view_vote',
                ],
            ],
        ],
    ],
    'manage_bbs' => [
        'name'   => '论坛权限',
        'manage' => true,
        'sub'    => [
            'bbs' => [
                'name'  => '论坛管理权限',
                'items' => [
                    'manage_level', 'operate_thread', 'force_operate_reason',
                ],
            ],
            'fresh' => [
                'name'  => '新鲜事管理权限',
                'items' => ['fresh_delete'],
            ],
            'tag' => [
                'name'  => '话题管理权限',
                'items' => [
                    'tag_allow_edit', 'tag_allow_manage',
                ],
            ],
        ],
    ],
    'manage_design' => [
        'name'   => '门户权限',
        'manage' => true,
        'sub'    => [
            'panel' => [
                'name'  => '门户管理权限',
                'items' => [
                    'design_allow_manage',
                ],
            ],
        ],
    ],
    'manage_user' => [
        'name'   => '用户权限',
        'manage' => true,
        'sub'    => [
        ],
    ],
    'manage_panel' => [
        'name'   => '前台管理',
        'manage' => true,
        'sub'    => [
            'panel' => [
                'name'  => '前台管理权限',
                'items' => [
                    'panel_bbs_manage', 'panel_user_manage', 'panel_report_manage', 'panel_recycle_manage', 'panel_log_manage',
                ],
            ],
        ],
    ],

    'other' => [
        'name' => '其他权限',
        'sub'  => [
        ],
    ],
];
