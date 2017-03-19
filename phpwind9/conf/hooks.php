<?php

defined('WEKIT_VERSION') or exit(403);
/*
 * 系统hook配置文件
 */
return [
    'c_post_run' => [
        'description' => '发表帖子展示页',
        'param'       => [],
        'interface'   => '',
        'list'        => [
            'poll' => [
                'class'       => PwPostDoPollInjector::class,
                'method'      => 'run',
                'expression'  => 'special.get==poll',
                'description' => '投票帖展示',
            ],
        ],
    ],
    'c_post_doadd' => [
        'description' => '发表帖子提交页',
        'param'       => [],
        'interface'   => '',
        'list'        => [
            'poll' => [
                'class'       => PwPostDoPollInjector::class,
                'method'      => 'doadd',
                'expression'  => 'special.post==poll',
                'description' => '发投票帖',
            ],
            'att' => [
                'class'       => PwPostDoAttInjector::class,
                'method'      => 'run',
                'expression'  => 'flashatt.post!=0',
                'description' => '发帖传附件',
            ],
            'tag' => [
                'class'       => PwPostDoTagInjector::class,
                'method'      => 'doadd',
                'description' => '帖子发布 - 话题相关',
            ],
            'word' => [
                'class'       => PwPostDoWordInjector::class,
                'method'      => 'doadd',
                'description' => '帖子发布 - 敏感词',
            ],
        ],
    ],
    'c_post_doreply' => [
        'description' => '发表回复提交页',
        'param'       => [],
        'interface'   => '',
        'list'        => [
            'att' => [
                'class'       => PwPostDoAttInjector::class,
                'method'      => 'run',
                'expression'  => 'flashatt.post!=0',
                'description' => '回复发布 - 附件',
            ],
            'dolike_fast_reply' => [
                'class'       => PwLikeDoFreshInjector::class,
                'method'      => 'run',
                'expression'  => 'isfresh.post==1',
                'description' => '回复发布 - 喜欢',
            ],
            'dolike_reply_lastpid' => [
                'class'       => PwLikeDoReplyInjector::class,
                'method'      => 'run',
                'expression'  => 'from_type.post==like',
                'description' => '回复发布 - 最后喜欢的回复',
            ],
            'word' => [
                'class'       => PwPostDoWordInjector::class,
                'method'      => 'doadd',
                'description' => '帖子发布 - 敏感词',
            ],
        ],
    ],
    'c_post_modify' => [
        'description' => '帖子编辑页面',
        'param'       => [],
        'interface'   => '',
        'list'        => [
            'poll' => [
                'class'       => PwPostDoPollInjector::class,
                'method'      => 'modify',
                'expression'  => 'service:special==poll',
                'description' => '帖子编辑 - 投票帖',
            ],
        ],
    ],
    'c_post_domodify' => [
        'description' => '帖子编辑提交页面',
        'param'       => [],
        'interface'   => '',
        'list'        => [
            'poll' => [
                'class'       => PwPostDoPollInjector::class,
                'method'      => 'domodify',
                'expression'  => 'service:special==poll',
                'description' => '帖子编辑提交 - 投票帖',
            ],
            'att' => [
                'class'       => PwPostDoAttInjector::class,
                'method'      => 'domodify',
                'description' => '帖子编辑 - 附件',
            ],
            'tag' => [
                'class'       => PwPostDoTagInjector::class,
                'method'      => 'domodify',
                'description' => '帖子编辑 - 话题',
            ],
            'word' => [
                'class'       => PwPostDoWordInjector::class,
                'method'      => 'doadd',
                'description' => '帖子发布 - 敏感词',
            ],
        ],
    ],
    'c_index_run' => [
        'description' => '新帖列表页',
        'param'       => [],
        'interface'   => '',
        'list'        => [
        ],
    ],
    'c_cate_run' => [
        'description' => '分类帖子列表页',
        'param'       => [],
        'interface'   => '',
        'list'        => [
        ],
    ],
    'c_thread_run' => [
        'description' => '版块帖子列表页',
        'param'       => [],
        'interface'   => '',
        'list'        => [
        ],
    ],
    'c_read_run' => [
        'description' => '帖子阅读页',
        'param'       => [],
        'interface'   => '',
        'list'        => [
            'poll' => [
                'class'       => PwThreadDisplayDoPollInjector::class,
                'method'      => 'run',
                'expression'  => 'service:thread.info.special==poll',
                'description' => '帖子阅读页 - 投票帖',
            ],
            'like' => [
                'class'       => PwThreadDisplayDoLikeInjector::class,
                'method'      => 'run',
                'expression'  => 'service:thread.info.like_count!=0',
                'description' => '帖子阅读页 - 喜欢',
            ],
            'medal' => [
                'class'       => PwThreadDisplayDoMedalInjector::class,
                'method'      => 'run',
                'expression'  => 'config:site.medal.isopen==1',
                'description' => '帖子阅读页 - 勋章',
            ],
            'word' => [
                'class'       => PwThreadDisplayDoWordInjector::class,
                'expression'  => 'service:thread.info.word_version==0',
                'description' => '帖子阅读页 - 替换敏感词',
            ],
        ],
    ],
    'c_register' => [
        'description' => '注册页面',
        'param'       => [],
        'interface'   => PwBaseHookInjector::class,
        'list'        => [
            'invite' => [
                'class'      => PwRegisterDoInviteInjector::class,
                'method'     => 'run',
                'expression' => 'service:isOpenInvite==1',
            ],
            'inviteFriend' => [
                'class'  => PwRegisterDoInviteFriendInjector::class,
                'method' => 'run',
            ],
            'verifyMobile' => [
                'class'  => PwRegisterDoVerifyMobileInjector::class,
                'method' => 'run',
            ],
        ],
    ],
    'c_fresh_post' => [
        'description' => '在新鲜事页面发布帖子',
        'param'       => [],
        'interface'   => '',
        'list'        => [
            'att' => [
                'class'      => PwPostDoAttInjector::class,
                'method'     => 'run',
                'expression' => 'flashatt.post!=0',
            ],
        ],
    ],
    'c_profile_extends_run' => [
        'description' => '用户菜单功能扩展-展示',
        'param'       => [],
        'list'        => [
        ],
    ],
    'c_profile_extends_dorun' => [
        'description' => '用户菜单功能扩展-执行',
        'param'       => [],
        'list'        => [
        ],
    ],
    'c_login_dorun' => [
        'description' => '用户登录，表现层',
        'param'       => [],
        'interface'   => '',
        'list'        => [
            'inviteFriend' => [
                'class'  => PwLoginDoInviteFriendInjector::class,
                'method' => 'run',
            ],
        ],
    ],
    'm_PwRegisterService' => [
        'description' => '注册Service钩子',
        'param'       => [],
        'interface'   => PwRegisterDoBase::class,
        'list'        => [
            'bbsinfo' => [
                'class'       => PwRegisterDoUpdateBbsInfo::class,
                'description' => '注册后期：更新站点信息',
            ],
        ],
    ],
    'm_PwTopicPost' => [
        'description' => '发表帖子',
        'param'       => [],
        'interface'   => PwPostDoBase::class,
        'list'        => [
            'fresh' => [
                'class'       => PwPostDoFresh::class,
                'description' => '新鲜事',
            ],
            'task' => [
                'class'       => PwTaskBbsThreadDo::class,
                'expression'  => 'config:site.task.isOpen==1',
                'description' => '发帖做任务',
            ],
            'behavior' => [
                'class'       => PwMiscThreadDo::class,
                'loadway'     => 'load',
                'description' => '记录发帖行为',
            ],
            'medal' => [
                'class'       => PwMedalThreadDo::class,
                'description' => '发帖做勋章',
            ],
            'remind' => [
                'class' => PwPostDoRemind::class,
            ],
            'word' => [
                'class'       => PwReplyDoWord::class,
                'description' => '回复-敏感词',
            ],
        ],
    ],
    'm_PwReplyPost' => [
        'description' => '发表回复',
        'param'       => [],
        'interface'   => PwPostDoBase::class,
        'list'        => [
            'task' => [
                'expression'  => 'config:site.task.isOpen==1',
                'class'       => PwTaskBbsPostDo::class,
                'description' => '发回复做任务',
            ],
            'behavior' => [
                'class'       => PwMiscPostDo::class,
                'loadway'     => 'load',
                'description' => '记录发回复行为',
            ],
            'medal' => [
                'class'       => PwMedalPostDo::class,
                'description' => '发回复做勋章任务',
            ],
            'remind' => [
                'class'       => PwReplyDoRemind::class,
                'description' => '回复-话题',
            ],
            'notice' => [
                'class'       => PwReplyDoNotice::class,
                'description' => '回复-通知',
            ],
            'word' => [
                'class'       => PwReplyDoWord::class,
                'description' => '回复-敏感词',
            ],
        ],
    ],
    'm_PwThreadList' => [
        'description' => '帖子列表页',
        'param'       => [],
        'interface'   => PwThreadListDoBase::class,
        'list'        => [
            'hits' => [
                'class'       => PwThreadListDoHits::class,
                'description' => '点击率实时更新显示',
                'expression'  => 'config:bbs.read.hit_update==1',
            ],
        ],
    ],
    'm_PwThreadDisplay' => [
        'description' => '帖子内容展示',
        'param'       => [],
        'interface'   => PwThreadDisplayDoBase::class,
        'list'        => [
            'hits' => [
                'class'       => PwThreadDisplayDoHits::class,
                'description' => '点击率实时更新显示',
                'expression'  => 'config:bbs.read.hit_update==1',
            ],
        ],
    ],
    /*获取任务奖励钩子*/
    'm_task_gainreward' => [
        'description' => '领取任务',
        'param'       => [],
        'interface'   => PwTaskRewardDoBase::class,
        'list'        => [
            'group' => [
                'class'      => PwTaskGroupRewardDo::class,
                'expression' => 'service:type==group',
            ],
            'credit' => [
                'class'      => PwTaskCreditRewardDo::class,
                'expression' => 'service:type==credit',
            ],
        ],
    ],
    'm_PwMessageService' => [
        'description' => '消息服务',
        'param'       => [],
        'interface'   => PwMessageDoBase::class,
        'list'        => [
            'task' => [
                'expression' => 'config:site.task.isOpen==1',
                'class'      => PwTaskMemberMsgDo::class,
                'loadway'    => 'load',
            ],
        ],
    ],
    'm_PwLoginService' => [
        'description' => '用户登录之后的操作',
        'param'       => ['@param PwUserBo $userBo 登录用户的对象', '@param string $ip 登录的IP'],
        'interface'   => PwUserLoginDoBase::class,
        'list'        => [
            'autotask' => [
                'expression' => 'config:site.task.isOpen==1',
                'class'      => PwAutoTaskLoginDo::class,
                'loadway'    => 'load',
            ],
            'userbelong' => [
                'class'   => PwUserLoginDoBelong::class,
                'loadway' => 'load',
            ],
            'behavior' => [
                'class'   => PwMiscUserDo::class,
                'loadway' => 'load',
            ],
            'medal' => [
                'class'   => PwMedalUserDo::class,
                'loadway' => 'load',
            ],
            'updateOnline' => [
                'class'   => PwLoginDoUpdateOnline::class,
                'loadway' => 'load',
            ],
            'autounbancheck' => [
                'class'   => PwLoginDoUnbanCheck::class,
                'loadway' => 'load',
            ],
            /*
            'recommendUser' => array(
                'class' => PwRecommendUserDo::class,
                'loadway' => 'load'
            ),*/
        ],
    ],
    'm_PwFreshReplyByWeibo' => [
        'description' => '微博',
        'param'       => [],
        'interface'   => PwWeiboDoBase::class,
        'list'        => [
            'word' => [
                'class'       => PwWeiboDoWord::class,
                'description' => '微博-敏感词',
            ],
        ],
    ],
    's_PwThreadsDao_add' => [
        'description' => '增加一条帖子记录时，调用',
        'param'       => ['@param int $id 新增的帖子tid', '@param array $fields 帖子字段', '@return void'],
        'interface'   => '',
        'list'        => [
            'threadsIndex' => [
                'class'   => PwThreadsIndexDao::class,
                'method'  => 'addThread',
                'loadway' => 'loadDao',
            ],
            'threadsCateIndex' => [
                'class'   => PwThreadsCateIndexDao::class,
                'method'  => 'addThread',
                'loadway' => 'loadDao',
            ],
            'threadsDigestIndex' => [
                'class'   => PwThreadsDigestIndexDao::class,
                'method'  => 'addThread',
                'loadway' => 'loadDao',
            ],
        ],
    ],
    's_PwThreadsDao_update' => [
        'description' => '更新一条帖子记录时，调用',
        'param'       => ['@param int $id 帖子tid', '@param array $fields 更新的帖子字段数据', '@param array $increaseFields 递增的帖子字段数据', '@return void'],
        'interface'   => '',
        'list'        => [
            'threadsIndex' => [
                'class'   => PwThreadsIndexDao::class,
                'method'  => 'updateThread',
                'loadway' => 'loadDao',
            ],
            'threadsCateIndex' => [
                'class'   => PwThreadsCateIndexDao::class,
                'method'  => 'updateThread',
                'loadway' => 'loadDao',
            ],
            'threadsDigestIndex' => [
                'class'   => PwThreadsDigestIndexDao::class,
                'method'  => 'updateThread',
                'loadway' => 'loadDao',
            ],
        ],
    ],
    's_PwThreadsDao_batchUpdate' => [
        'description' => '批量更新多条帖子记录时，调用',
        'param'       => ['@param array $ids 帖子tid序列', '@param array $fields 更新的帖子字段数据', '@param array $increaseFields 递增的帖子字段数据', '@return void'],
        'interface'   => '',
        'list'        => [
            'threadsIndex' => [
                'class'   => PwThreadsIndexDao::class,
                'method'  => 'batchUpdateThread',
                'loadway' => 'loadDao',
            ],
            'threadsCateIndex' => [
                'class'   => PwThreadsCateIndexDao::class,
                'method'  => 'batchUpdateThread',
                'loadway' => 'loadDao',
            ],
            'threadsDigestIndex' => [
                'class'   => PwThreadsDigestIndexDao::class,
                'method'  => 'batchUpdateThread',
                'loadway' => 'loadDao',
            ],
        ],
    ],
    's_PwThreadsDao_revertTopic' => [
        'description' => '还原帖子时，调用',
        'param'       => ['@param array $tids 帖子tid序列', '@return void'],
        'interface'   => '',
        'list'        => [
            'threadsIndex' => [
                'class'   => PwThreadsIndexDao::class,
                'method'  => 'revertTopic',
                'loadway' => 'loadDao',
            ],
            'threadsCateIndex' => [
                'class'   => PwThreadsCateIndexDao::class,
                'method'  => 'revertTopic',
                'loadway' => 'loadDao',
            ],
            'threadsDigestIndex' => [
                'class'   => PwThreadsDigestIndexDao::class,
                'method'  => 'revertTopic',
                'loadway' => 'loadDao',
            ],
        ],
    ],
    's_PwThreadsDao_delete' => [
        'description' => '删除一个帖子时，调用',
        'param'       => ['@param int $id 帖子tid', '@return void'],
        'interface'   => '',
        'list'        => [
            'threadsIndex' => [
                'class'   => PwThreadsIndexDao::class,
                'method'  => 'deleteThread',
                'loadway' => 'loadDao',
            ],
            'threadsCateIndex' => [
                'class'   => PwThreadsCateIndexDao::class,
                'method'  => 'deleteThread',
                'loadway' => 'loadDao',
            ],
            'threadsDigestIndex' => [
                'class'   => PwThreadsDigestIndexDao::class,
                'method'  => 'deleteThread',
                'loadway' => 'loadDao',
            ],
        ],
    ],
    's_PwThreadsDao_batchDelete' => [
        'description' => '批量删除多个帖子时，调用',
        'param'       => ['@param array $ids 帖子tid序列', '@return void'],
        'interface'   => '',
        'list'        => [
            'threadsIndex' => [
                'class'   => PwThreadsIndexDao::class,
                'method'  => 'batchDeleteThread',
                'loadway' => 'loadDao',
            ],
            'threadsCateIndex' => [
                'class'   => PwThreadsCateIndexDao::class,
                'method'  => 'batchDeleteThread',
                'loadway' => 'loadDao',
            ],
            'threadsDigestIndex' => [
                'class'   => PwThreadsDigestIndexDao::class,
                'method'  => 'batchDeleteThread',
                'loadway' => 'loadDao',
            ],
        ],
    ],
    's_addFollow' => [
        'description' => '当发生关注操作时，调用',
        'param'       => ['@param int $uid 用户', '@param int $touid 被关注用户', '@return void'],
        'interface'   => '',
        'list'        => [
            'medal' => [
                'class'   => PwMedalFansDo::class,
                'method'  => 'addFollow',
                'loadway' => 'load',
            ],
            'task' => [
                'expression' => 'config:site.task.isOpen==1',
                'class'      => PwTaskMemberFansDo::class,
                'method'     => 'addFollow',
                'loadway'    => 'load',
            ],
            'message' => [
                'class'   => PwNoticeFansDo::class,
                'method'  => 'addFollow',
                'loadway' => 'load',
            ],
        ],
    ],
    's_deleteFollow' => [
        'description' => '当发生取消关注操作时，调用',
        'param'       => ['@param int $uid 用户', '@param int $touid 被关注用户', '@return void'],
        'interface'   => '',
        'list'        => [
            'medal' => [
                'class'   => PwMedalFansDo::class,
                'method'  => 'delFollow',
                'loadway' => 'load',
            ],
            /*
            'recommend' => array(
                'class' => PwRecommendAttentionDo::class,
                'method' => 'delFollow',
                'loadway' => 'load'
            ),*/
        ],
    ],

    's_PwTaskDao_update' => [
        'description' => '更新一条任务记录时，调用',
        'param'       => ['@param int $id 帖子tid', '@param array $fields 更新的任务字段数据', '@param array $increaseFields 递增的任务字段数据', '@return void'],
        'interface'   => '',
        'list'        => [
            'TaskUser' => [
                'class'   => PwTaskUserDao::class,
                'method'  => 'updateIsPeriod',
                'loadway' => 'loadDao',
            ],
        ],
    ],
    's_profile_editUser' => [
        'description' => '更新用户资料时，调用',
        'param'       => ['@param PwUserInfoDm $dm', '@return void'],
        'interface'   => '',
        'list'        => [
            'task' => [
                'expression' => 'config:site.task.isOpen==1',
                'class'      => PwTaskProfileConditionDo::class,
                'loadway'    => 'load',
                'method'     => 'editUser',
            ],
        ],
    ],
    's_update_avatar' => [
        'description' => '更新用户头像时，调用',
        'param'       => ['@param int $uid 用户id', '@return void'],
        'interface'   => '',
        'list'        => [
            'task' => [
                'expression' => 'config:site.task.isOpen==1',
                'class'      => PwTaskMemberAvatarDo::class,
                'loadway'    => 'load',
                'method'     => 'uploadAvatar',
            ],
        ],
    ],
    's_PwUser_delete' => [
        'description' => '删除用户时，调用',
        'param'       => ['@param int $uid 用户id', '@return void'],
        'interface'   => '',
        'list'        => [
            'ban' => [
                'class'   => PwUserDoBan::class,
                'method'  => 'deleteBan',
                'loadway' => 'load',
            ],
            'belong' => [
                'class'   => PwUserDoBelong::class,
                'method'  => 'deleteUser',
                'loadway' => 'load',
            ],
            'registerCheck' => [
                'class'   => PwUserDoRegisterCheck::class,
                'method'  => 'deleteUser',
                'loadway' => 'load',
            ],
            'activeCode' => [
                'class'   => PwUserActiveCode::class,
                'method'  => 'deleteInfoByUid',
                'loadway' => 'load',
            ],
            'task' => [
                'class'   => PwTaskUser::class,
                'method'  => 'deleteByUid',
                'loadway' => 'load',
            ],
            'usertag' => [
                'class'   => PwUserTagRelation::class,
                'method'  => 'deleteRelationByUid',
                'loadway' => 'load',
            ],
            'mobile' => [
                'class'   => PwUserMobile::class,
                'method'  => 'deleteByUid',
                'loadway' => 'load',
            ],
        ],
    ],
    's_PwUser_batchDelete' => [
        'description' => '批量删除用户时，调用',
        'param'       => ['@param array $uids 用户id序列', '@return void'],
        'interface'   => '',
        'list'        => [
            'ban' => [
                'class'   => PwUserDoBan::class,
                'method'  => 'batchDeleteBan',
                'loadway' => 'load',
            ],
            'belong' => [
                'class'   => PwUserDoBelong::class,
                'method'  => 'batchDeleteUser',
                'loadway' => 'load',
            ],
            'registerCheck' => [
                'class'   => PwUserDoRegisterCheck::class,
                'method'  => 'batchDeleteUser',
                'loadway' => 'load',
            ],
            'task' => [
                'class'   => PwTaskUser::class,
                'method'  => 'batchDeleteByUid',
                'loadway' => 'load',
            ],
            'usertag' => [
                'class'   => PwUserTagRelation::class,
                'method'  => 'batchDeleteRelationByUids',
                'loadway' => 'load',
            ],
        ],
    ],
    's_PwUser_add' => [
        'description' => '添加用户时，调用',
        'param'       => ['@param PwUserInfoDm $dm', '@return void'],
        'interface'   => '',
        'list'        => [
            'belong' => [
                'class'   => PwUserDoBelong::class,
                'method'  => 'editUser',
                'loadway' => 'load',
            ],
        ],
    ],
    's_PwUser_update' => [
        'description' => '更新用户信息时，调用',
        'param'       => ['@param PwUserInfoDm $dm', '@return void'],
        'interface'   => '',
        'list'        => [
            'belong' => [
                'class'   => PwUserDoBelong::class,
                'method'  => 'editUser',
                'loadway' => 'load',
            ],
        ],
    ],
    's_PwUserDataDao_update' => [
        'description' => '用户数据更新时，调用',
        'param'       => ['@param int $id 用户id', '@param array $fields 更新的用户字段数据', '@param array $increaseFields 递增的用户字段数据', '@return void'],
        'interface'   => '',
        'list'        => [
            'level' => [
                'class'   => PwUserGroupsService::class,
                'method'  => 'updateLevel',
                'loadway' => 'load',
            ],
            'autoBan' => [
                'class'      => PwUserBanService::class,
                'method'     => 'autoBan',
                'loadway'    => 'load',
                'expression' => 'config:site.autoForbidden.open==1',
            ],
        ],
    ],
    's_PwUserGroups_update' => [
        'description' => '用户组资料更新时，调用',
        'param'       => ['@param int $gid 用户组id', '@return void'],
        'interface'   => '',
        'list'        => [
            'usergroup' => [
                'class'   => PwUserGroupsService::class,
                'method'  => 'updateGroupCacheByHook',
                'loadway' => 'load',
            ],
        ],
    ],
    's_PwUserGroupsDao_delete' => [
        'description' => '删除用户组时，调用',
        'param'       => ['@param int $gid 用户组id', '@return void'],
        'interface'   => '',
        'list'        => [
            'usergroup' => [
                'class'   => PwUserGroupsService::class,
                'method'  => 'deleteGroupCacheByHook',
                'loadway' => 'load',
            ],
        ],
    ],
    's_PwUserGroupPermission_update' => [
        'description' => '用户组权限变更时，调用',
        'param'       => ['@param PwUserPermissionDm $dm', '@return void'],
        'interface'   => '',
        'list'        => [
            'usergroup_permission' => [
                'class'   => PwUserGroupsService::class,
                'method'  => 'updatePermissionCacheByHook',
                'loadway' => 'load',
            ],
        ],
    ],
    's_PwLikeService_delLike' => [
        'description' => '删除喜欢',
        'list'        => [
            'behavior' => [
                'class'   => PwMiscLikeDo::class,
                'method'  => 'delLike',
                'loadway' => 'load',
            ],
            'medal' => [
                'class'   => PwMedalLikeDo::class,
                'method'  => 'delLike',
                'loadway' => 'load',
            ],
        ],
    ],
    's_PwLikeService_addLike' => [
        'description' => '添加喜欢',
        'list'        => [
            'task' => [
                'expression' => 'config:site.task.isOpen==1',
                'class'      => PwTaskBbsLikeDo::class,
                'method'     => 'addLike',
                'loadway'    => 'load',
            ],
            'behavior' => [
                'class'   => PwMiscLikeDo::class,
                'method'  => 'addLike',
                'loadway' => 'load',
            ],
            'medal' => [
                'class'   => PwMedalLikeDo::class,
                'method'  => 'addLike',
                'loadway' => 'load',
            ],
        ],
    ],
    's_PwUserTagRelationDao_deleteRelation' => [
        'description' => '删除用户标签的关系，调用',
        'param'       => ['@param int $tag_id 标签id', '@return void'],
        'interface'   => '',
        'list'        => [
            'PwUserTag' => [
                'class'   => PwUserTagDao::class,
                'method'  => 'updateTag',
                'loadway' => 'loadDao',
            ],
        ],
    ],
    's_PwUserTagDao_deleteTag' => [
        'description' => '删除用户标签时，调用',
        'param'       => ['@param int $tag_id 标签id', '@return void'],
        'interface'   => '',
        'list'        => [
            'PwUserTagRelation' => [
                'class'   => PwUserTagRelationDao::class,
                'method'  => 'deleteRelationByTagid',
                'loadway' => 'loadDao',
            ],
        ],
    ],
    's_PwUserTagDao_batchDeleteTag' => [
        'description' => '批量删除用户标签时，调用',
        'param'       => ['@param array $tag_ids 标签id序列', '@return void'],
        'interface'   => '',
        'list'        => [
            'PwUserTagRelation' => [
                'class'   => PwUserTagRelationDao::class,
                'method'  => 'batchDeleteRelationByTagids',
                'loadway' => 'loadDao',
            ],
        ],
    ],
    's_PwUserTagRelation_batchDeleteRelation' => [
        'description' => '删除用户标签关系的时候',
        'param'       => ['@param array $tag_ids ', '@param PwUserTagRelation ', '@return void'],
        'interface'   => '',
        'list'        => [
            'PwDeleteRelationDoUpdateTag' => [
                'class'   => PwDeleteRelationDoUpdateTag::class,
                'method'  => 'batchDeleteRelation',
                'loadway' => 'load',
            ],
        ],
    ],
    's_PwUserTagRelation_deleteRelationByUid' => [
        'description' => '根据用户ID删除用户标签关系',
        'param'       => ['@param int $uid ', '@return void'],
        'interface'   => '',
        'list'        => [
            'PwDeleteRelationDoUpdateTag' => [
                'class'   => PwDeleteRelationDoUpdateTag::class,
                'method'  => 'deleteRelationByUid',
                'loadway' => 'load',
            ],
        ],
    ],
    's_PwUserTagRelation_batchDeleteRelationByUids' => [
        'description' => '根据用户ID列表批量删除用户标签关系',
        'param'       => ['@param array $uid ', '@return void'],
        'interface'   => '',
        'list'        => [
            'PwDeleteRelationDoUpdateTag' => [
                'class'   => PwDeleteRelationDoUpdateTag::class,
                'method'  => 'batchDeleteRelationByUids',
                'loadway' => 'load',
            ],
        ],
    ],
    /*添加表情*/
    's_PwEmotionDao_add' => [
        'description' => '添加表情时，调用',
        'param'       => ['@param int $id id', '@param array $fields 字段信息', '@return void'],
        'interface'   => '',
        'list'        => [
            'addEmotion' => [
                'class'   => PwEmotionService::class,
                'method'  => 'updateCache',
                'loadway' => 'load',
            ],
        ],
    ],
    /*编辑表情*/
    's_PwEmotionDao_update' => [
        'description' => '编辑表情时，调用',
        'param'       => ['@param int $id 表情id', '@param array $fields 字段信息', '@param array $increaseFields 字段信息', '@return void'],
        'interface'   => '',
        'list'        => [
            'addEmotion' => [
                'class'   => PwEmotionService::class,
                'method'  => 'updateCache',
                'loadway' => 'load',
            ],
        ],
    ],
    /*删除表情*/
    's_PwEmotionDao_delete' => [
        'description' => '删除表情时，调用',
        'param'       => ['@param int $id 表情id', '@return void'],
        'interface'   => '',
        'list'        => [
            'addEmotion' => [
                'class'   => PwEmotionService::class,
                'method'  => 'updateCache',
                'loadway' => 'load',
            ],
        ],
    ],
    's_PwEmotionDao_deleteEmotionByCatid' => [
        'description' => '删除一组表情时，调用',
        'param'       => ['@param int $cateId 表情组id', '@return void'],
        'interface'   => '',
        'list'        => [
            'addEmotion' => [
                'class'   => PwEmotionService::class,
                'method'  => 'updateCache',
                'loadway' => 'load',
            ],
        ],
    ],
    's_PwConfigDao_update' => [
        'description' => '全局配置更新时，调用',
        'param'       => ['@param string $namespace 配置域'],
        'interface'   => '',
        'list'        => [
            'configCache' => [
                'class'   => PwConfigService::class,
                'method'  => 'updateConfig',
                'loadway' => 'load',
            ],
        ],
    ],
    's_PwThreadType' => [
        'description' => '获取帖子扩展类型时，调用',
        'param'       => ['@param array $tType 帖子类型', '@return array'],
        'interface'   => '',
        'list'        => [],
    ],
    's_punch' => [
        'description' => '打卡时，调用',
        'param'       => ['@param PwUserInfoDm $dm', '@return void'],
        'interface'   => '',
        'list'        => [
            'task' => [
                'expression' => 'config:site.task.isOpen==1',
                'class'      => PwTaskMemberPunchDo::class,
                'method'     => 'doPunch',
                'loadway'    => 'load',
            ],
        ],
    ],
    /*扩展存储类型*/
    's_PwStorage_getStorages' => [ //todo
        'description' => '获取附件存储类型',
        'param'       => ['@param array $storages', '@return array'],
        'interface'   => '',
        'list'        => [
        ],
    ],
    's_PwThreadManageDoCopy' => [ //todo
        'description' => '帖子复制',
        'param'       => ['@param PwThreadManage $srv', '@return void'],
        'interface'   => 'PwThreadManageCopyDoBase',
        'list'        => [
            'poll' => [
                'class'      => PwThreadManageCopyDoPoll::class,
                'method'     => 'copyThread',
                'loadway'    => 'load',
                'expression' => 'service:special==poll',
            ],
            'att' => [
                'class'      => PwThreadManageCopyDoAtt::class,
                'method'     => 'copyThread',
                'loadway'    => 'load',
                'expression' => 'service:ifupload!=0',
            ],
        ],
    ],
    /* 用户退出之前的更新 */
    's_PwUserService_logout' => [
        'description' => '退出登录',
        'param'       => ['@param PwUserBo $loginUser', '@return void'],
        'interface'   => 'PwLogoutDoBase',
        'list'        => [
            'updatelastvist' => [
                'class'   => PwLogoutDoUpdateLastvisit::class,
                'method'  => 'beforeLogout',
                'loadway' => 'load',
            ],
            'updateOnline' => [
                'class'   => PwLogoutDoUpdateOnline::class,
                'method'  => 'beforeLogout',
                'loadway' => 'load',
            ],
        ],
    ],
    's_PwEditor_app' => [
        'description' => '编辑器配置扩展',
        'param'       => ['@param array $var', '@return array'],
        'list'        => [
        ],
    ],
    's_PwCreditOperationConfig' => [
        'description' => '积分策略配置',
        'param'       => ['@param array $config 积分策略配置', '@return array'],
        'list'        => [
        ],
    ],
    's_seo_config' => [
        'description' => 'seo优化扩展',
        'param'       => ['@param array $config seo扩展配置', '@return array'],
        'list'        => [
        ],
    ],
    's_PwUserBehaviorDao_replaceInfo' => [
        'description' => '用户行为更新扩展',
        'param'       => ['@param array $data 用户行为数据', '@return '],
        'list'        => [
            'task' => [
                'class'      => PwTaskService::class,
                'method'     => 'sendAutoTask',
                'loadway'    => 'load',
                'expression' => 'config:site.task.isOpen==1',
            ],
        ],
    ],
    's_admin_menu' => [
        'description' => '后台菜单扩展',
        'param'       => ['@param array $config 后台菜单配置', '@return array'],
        'list'        => [
        ],
    ],
    's_permissionCategoryConfig' => [
        'description' => '用户组根权限',
        'param'       => ['@param array $config 用户组根权限', '@return array'],
        'list'        => [
        ],
    ],
    's_permissionConfig' => [
        'description' => '用户组权限',
        'param'       => ['@param array $config 用户组权限', '@return array'],
        'list'        => [
        ],
    ],
    's_PwMobileService_checkVerify' => [
        'description' => '验证手机完成',
        'param'       => ['@param int $mobile'],
        'list'        => [
        ],
    ],
    's_header_nav' => [
        'description' => '全局头部导航',
        'param'       => [],
        'list'        => [
        ],
    ],
    's_header_info_1' => [
        'description' => '头部用户信息扩展点1',
        'param'       => [],
        'list'        => [
        ],
    ],
    's_header_info_2' => [
        'description' => '头部用户信息扩展点2',
        'param'       => [],
        'list'        => [
        ],
    ],
    's_header_my' => [
        'description' => '头部帐号的下拉',
        'param'       => [],
        'list'        => [
        ],
    ],
    's_footer' => [
        'description' => '全局底部',
        'param'       => [],
        'list'        => [
        ],
    ],
    's_space_nav' => [
        'description' => '个人空间导航扩展',
        'param'       => ['@param array $space', '@param string $src'],
        'list'        => [
        ],
    ],
    's_space_profile' => [
        'description' => '空间资料页面',
        'param'       => ['@param array $space'],
        'interface'   => '',
        'list'        => [ //这个顺序别改，pd要求的
            'education' => [
                'class'  => PwSpaceProfileDoEducation::class,
                'method' => 'createHtml',
            ],
            'work' => [
                'class'  => PwSpaceProfileDoWork::class,
                'method' => 'createHtml',
            ],
        ],
    ],
    's_profile_menus' => [
        'description' => '个人设置-菜单项扩展',
        'param'       => ['@param array $config 注册的菜单', '@return array'],
        'list'        => [
        ],
    ],

    's_attachment_watermark' => [
        'description' => '全局->水印设置->水印策略扩展',
        'param'       => ['@param array $config 已有的需要设置的策略,每一个扩展项格式:key=>title', '@return array'],
        'list'        => [
        ],
    ],
    's_verify_showverify' => [
        'description' => '全局->验证码->验证策略',
        'param'       => ['@param array $config 需要设置的策略,每一个扩展项格式:key=>title', '@return array'],
        'list'        => [
        ],
    ],
    /*手机短信扩展*/
    's_PwMobileService_getPlats' => [
        'description' => '手机短信 - 平台选择',
        'param'       => ['@param array $config 配置文件，可参考SRV:mobile.config.plat.php', '@return array'],
        'list'        => [
        ],
    ],
];
