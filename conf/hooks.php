<?php

defined('WEKIT_VERSION') or exit(403);
/*
 * 系统hook配置文件
 */
return array(
    'c_post_run' => array(
        'description' => '发表帖子展示页',
        'param'       => array(),
        'interface'   => '',
        'list'        => array(
            'poll' => array(
                'class'       => PwPostDoPollInjector::class,
                'method'      => 'run',
                'expression'  => 'special.get==poll',
                'description' => '投票帖展示',
            ),
        ),
    ),
    'c_post_doadd' => array(
        'description' => '发表帖子提交页',
        'param'       => array(),
        'interface'   => '',
        'list'        => array(
            'poll' => array(
                'class'       => PwPostDoPollInjector::class,
                'method'      => 'doadd',
                'expression'  => 'special.post==poll',
                'description' => '发投票帖',
            ),
            'att' => array(
                'class'       => PwPostDoAttInjector::class,
                'method'      => 'run',
                'expression'  => 'flashatt.post!=0',
                'description' => '发帖传附件',
            ),
            'tag' => array(
                'class'       => PwPostDoTagInjector::class,
                'method'      => 'doadd',
                'description' => '帖子发布 - 话题相关',
            ),
            'word' => array(
                'class'       => PwPostDoWordInjector::class,
                'method'      => 'doadd',
                'description' => '帖子发布 - 敏感词',
            ),
        ),
    ),
    'c_post_doreply' => array(
        'description' => '发表回复提交页',
        'param'       => array(),
        'interface'   => '',
        'list'        => array(
            'att' => array(
                'class'       => PwPostDoAttInjector::class,
                'method'      => 'run',
                'expression'  => 'flashatt.post!=0',
                'description' => '回复发布 - 附件',
            ),
            'dolike_fast_reply' => array(
                'class'       => PwLikeDoFreshInjector::class,
                'method'      => 'run',
                'expression'  => 'isfresh.post==1',
                'description' => '回复发布 - 喜欢',
            ),
            'dolike_reply_lastpid' => array(
                'class'       => PwLikeDoReplyInjector::class,
                'method'      => 'run',
                'expression'  => 'from_type.post==like',
                'description' => '回复发布 - 最后喜欢的回复',
            ),
            'word' => array(
                'class'       => PwPostDoWordInjector::class,
                'method'      => 'doadd',
                'description' => '帖子发布 - 敏感词',
            ),
        ),
    ),
    'c_post_modify' => array(
        'description' => '帖子编辑页面',
        'param'       => array(),
        'interface'   => '',
        'list'        => array(
            'poll' => array(
                'class'       => PwPostDoPollInjector::class,
                'method'      => 'modify',
                'expression'  => 'service:special==poll',
                'description' => '帖子编辑 - 投票帖',
            ),
        ),
    ),
    'c_post_domodify' => array(
        'description' => '帖子编辑提交页面',
        'param'       => array(),
        'interface'   => '',
        'list'        => array(
            'poll' => array(
                'class'       => PwPostDoPollInjector::class,
                'method'      => 'domodify',
                'expression'  => 'service:special==poll',
                'description' => '帖子编辑提交 - 投票帖',
            ),
            'att' => array(
                'class'       => PwPostDoAttInjector::class,
                'method'      => 'domodify',
                'description' => '帖子编辑 - 附件',
            ),
            'tag' => array(
                'class'       => PwPostDoTagInjector::class,
                'method'      => 'domodify',
                'description' => '帖子编辑 - 话题',
            ),
            'word' => array(
                'class'       => PwPostDoWordInjector::class,
                'method'      => 'doadd',
                'description' => '帖子发布 - 敏感词',
            ),
        ),
    ),
    'c_index_run' => array(
        'description' => '新帖列表页',
        'param'       => array(),
        'interface'   => '',
        'list'        => array(
        ),
    ),
    'c_cate_run' => array(
        'description' => '分类帖子列表页',
        'param'       => array(),
        'interface'   => '',
        'list'        => array(
        ),
    ),
    'c_thread_run' => array(
        'description' => '版块帖子列表页',
        'param'       => array(),
        'interface'   => '',
        'list'        => array(
        ),
    ),
    'c_read_run' => array(
        'description' => '帖子阅读页',
        'param'       => array(),
        'interface'   => '',
        'list'        => array(
            'poll' => array(
                'class'       => PwThreadDisplayDoPollInjector::class,
                'method'      => 'run',
                'expression'  => 'service:thread.info.special==poll',
                'description' => '帖子阅读页 - 投票帖',
            ),
            'like' => array(
                'class'       => PwThreadDisplayDoLikeInjector::class,
                'method'      => 'run',
                'expression'  => 'service:thread.info.like_count!=0',
                'description' => '帖子阅读页 - 喜欢',
            ),
            'medal' => array(
                'class'       => PwThreadDisplayDoMedalInjector::class,
                'method'      => 'run',
                'expression'  => 'config:site.medal.isopen==1',
                'description' => '帖子阅读页 - 勋章',
            ),
            'word' => array(
                'class'       => PwThreadDisplayDoWordInjector::class,
                'expression'  => 'service:thread.info.word_version==0',
                'description' => '帖子阅读页 - 替换敏感词',
            ),
        ),
    ),
    'c_register' => array(
        'description' => '注册页面',
        'param'       => array(),
        'interface'   => PwBaseHookInjector::class,
        'list'        => array(
            'invite' => array(
                'class'      => PwRegisterDoInviteInjector::class,
                'method'     => 'run',
                'expression' => 'service:isOpenInvite==1',
            ),
            'inviteFriend' => array(
                'class'  => PwRegisterDoInviteFriendInjector::class,
                'method' => 'run',
            ),
            'verifyMobile' => array(
                'class'  => PwRegisterDoVerifyMobileInjector::class,
                'method' => 'run',
            ),
        ),
    ),
    'c_fresh_post' => array(
        'description' => '在新鲜事页面发布帖子',
        'param'       => array(),
        'interface'   => '',
        'list'        => array(
            'att' => array(
                'class'      => PwPostDoAttInjector::class,
                'method'     => 'run',
                'expression' => 'flashatt.post!=0',
            ),
        ),
    ),
    'c_profile_extends_run' => array(
        'description' => '用户菜单功能扩展-展示',
        'param'       => array(),
        'list'        => array(
        ),
    ),
    'c_profile_extends_dorun' => array(
        'description' => '用户菜单功能扩展-执行',
        'param'       => array(),
        'list'        => array(
        ),
    ),
    'c_login_dorun' => array(
        'description' => '用户登录，表现层',
        'param'       => array(),
        'interface'   => '',
        'list'        => array(
            'inviteFriend' => array(
                'class'  => PwLoginDoInviteFriendInjector::class,
                'method' => 'run',
            ),
        ),
    ),
    'm_PwRegisterService' => array(
        'description' => '注册Service钩子',
        'param'       => array(),
        'interface'   => PwRegisterDoBase::class,
        'list'        => array(
            'bbsinfo' => array(
                'class'       => PwRegisterDoUpdateBbsInfo::class,
                'description' => '注册后期：更新站点信息',
            ),
        ),
    ),
    'm_PwTopicPost' => array(
        'description' => '发表帖子',
        'param'       => array(),
        'interface'   => PwPostDoBase::class,
        'list'        => array(
            'fresh' => array(
                'class'       => PwPostDoFresh::class,
                'description' => '新鲜事',
            ),
            'task' => array(
                'class'       => PwTaskBbsThreadDo::class,
                'expression'  => 'config:site.task.isOpen==1',
                'description' => '发帖做任务',
            ),
            'behavior' => array(
                'class'       => PwMiscThreadDo::class,
                'loadway'     => 'load',
                'description' => '记录发帖行为',
            ),
            'medal' => array(
                'class'       => PwMedalThreadDo::class,
                'description' => '发帖做勋章',
            ),
            'remind' => array(
                'class' => PwPostDoRemind::class,
            ),
            'word' => array(
                'class'       => PwReplyDoWord::class,
                'description' => '回复-敏感词',
            ),
        ),
    ),
    'm_PwReplyPost' => array(
        'description' => '发表回复',
        'param'       => array(),
        'interface'   => PwPostDoBase::class,
        'list'        => array(
            'task' => array(
                'expression'  => 'config:site.task.isOpen==1',
                'class'       => PwTaskBbsPostDo::class,
                'description' => '发回复做任务',
            ),
            'behavior' => array(
                'class'       => PwMiscPostDo::class,
                'loadway'     => 'load',
                'description' => '记录发回复行为',
            ),
            'medal' => array(
                'class'       => PwMedalPostDo::class,
                'description' => '发回复做勋章任务',
            ),
            'remind' => array(
                'class'       => PwReplyDoRemind::class,
                'description' => '回复-话题',
            ),
            'notice' => array(
                'class'       => PwReplyDoNotice::class,
                'description' => '回复-通知',
            ),
            'word' => array(
                'class'       => PwReplyDoWord::class,
                'description' => '回复-敏感词',
            ),
        ),
    ),
    'm_PwThreadList' => array(
        'description' => '帖子列表页',
        'param'       => array(),
        'interface'   => PwThreadListDoBase::class,
        'list'        => array(
            'hits' => array(
                'class'       => PwThreadListDoHits::class,
                'description' => '点击率实时更新显示',
                'expression'  => 'config:bbs.read.hit_update==1',
            ),
        ),
    ),
    'm_PwThreadDisplay' => array(
        'description' => '帖子内容展示',
        'param'       => array(),
        'interface'   => PwThreadDisplayDoBase::class,
        'list'        => array(
            'hits' => array(
                'class'       => PwThreadDisplayDoHits::class,
                'description' => '点击率实时更新显示',
                'expression'  => 'config:bbs.read.hit_update==1',
            ),
        ),
    ),
    /*获取任务奖励钩子*/
    'm_task_gainreward' => array(
        'description' => '领取任务',
        'param'       => array(),
        'interface'   => PwTaskRewardDoBase::class,
        'list'        => array(
            'group' => array(
                'class'      => PwTaskGroupRewardDo::class,
                'expression' => 'service:type==group',
            ),
            'credit' => array(
                'class'      => PwTaskCreditRewardDo::class,
                'expression' => 'service:type==credit',
            ),
        ),
    ),
    'm_PwMessageService' => array(
        'description' => '消息服务',
        'param'       => array(),
        'interface'   => PwMessageDoBase::class,
        'list'        => array(
            'task' => array(
                'expression' => 'config:site.task.isOpen==1',
                'class'      => PwTaskMemberMsgDo::class,
                'loadway'    => 'load',
            ),
        ),
    ),
    'm_PwLoginService' => array(
        'description' => '用户登录之后的操作',
        'param'       => array('@param PwUserBo $userBo 登录用户的对象', '@param string $ip 登录的IP'),
        'interface'   => PwUserLoginDoBase::class,
        'list'        => array(
            'autotask' => array(
                'expression' => 'config:site.task.isOpen==1',
                'class'      => PwAutoTaskLoginDo::class,
                'loadway'    => 'load',
            ),
            'userbelong' => array(
                'class'   => PwUserLoginDoBelong::class,
                'loadway' => 'load',
            ),
            'behavior' => array(
                'class'   => PwMiscUserDo::class,
                'loadway' => 'load',
            ),
            'medal' => array(
                'class'   => PwMedalUserDo::class,
                'loadway' => 'load',
            ),
            'updateOnline' => array(
                'class'   => PwLoginDoUpdateOnline::class,
                'loadway' => 'load',
            ),
            'autounbancheck' => array(
                'class'   => PwLoginDoUnbanCheck::class,
                'loadway' => 'load',
            ),
            /*
            'recommendUser' => array(
                'class' => PwRecommendUserDo::class,
                'loadway' => 'load'
            ),*/
        ),
    ),
    'm_PwFreshReplyByWeibo' => array(
        'description' => '微博',
        'param'       => array(),
        'interface'   => PwWeiboDoBase::class,
        'list'        => array(
            'word' => array(
                'class'       => PwWeiboDoWord::class,
                'description' => '微博-敏感词',
            ),
        ),
    ),
    's_PwThreadsDao_add' => array(
        'description' => '增加一条帖子记录时，调用',
        'param'       => array('@param int $id 新增的帖子tid', '@param array $fields 帖子字段', '@return void'),
        'interface'   => '',
        'list'        => array(
            'threadsIndex' => array(
                'class'   => PwThreadsIndexDao::class,
                'method'  => 'addThread',
                'loadway' => 'loadDao',
            ),
            'threadsCateIndex' => array(
                'class'   => PwThreadsCateIndexDao::class,
                'method'  => 'addThread',
                'loadway' => 'loadDao',
            ),
            'threadsDigestIndex' => array(
                'class'   => PwThreadsDigestIndexDao::class,
                'method'  => 'addThread',
                'loadway' => 'loadDao',
            ),
        ),
    ),
    's_PwThreadsDao_update' => array(
        'description' => '更新一条帖子记录时，调用',
        'param'       => array('@param int $id 帖子tid', '@param array $fields 更新的帖子字段数据', '@param array $increaseFields 递增的帖子字段数据', '@return void'),
        'interface'   => '',
        'list'        => array(
            'threadsIndex' => array(
                'class'   => PwThreadsIndexDao::class,
                'method'  => 'updateThread',
                'loadway' => 'loadDao',
            ),
            'threadsCateIndex' => array(
                'class'   => PwThreadsCateIndexDao::class,
                'method'  => 'updateThread',
                'loadway' => 'loadDao',
            ),
            'threadsDigestIndex' => array(
                'class'   => PwThreadsDigestIndexDao::class,
                'method'  => 'updateThread',
                'loadway' => 'loadDao',
            ),
        ),
    ),
    's_PwThreadsDao_batchUpdate' => array(
        'description' => '批量更新多条帖子记录时，调用',
        'param'       => array('@param array $ids 帖子tid序列', '@param array $fields 更新的帖子字段数据', '@param array $increaseFields 递增的帖子字段数据', '@return void'),
        'interface'   => '',
        'list'        => array(
            'threadsIndex' => array(
                'class'   => PwThreadsIndexDao::class,
                'method'  => 'batchUpdateThread',
                'loadway' => 'loadDao',
            ),
            'threadsCateIndex' => array(
                'class'   => PwThreadsCateIndexDao::class,
                'method'  => 'batchUpdateThread',
                'loadway' => 'loadDao',
            ),
            'threadsDigestIndex' => array(
                'class'   => PwThreadsDigestIndexDao::class,
                'method'  => 'batchUpdateThread',
                'loadway' => 'loadDao',
            ),
        ),
    ),
    's_PwThreadsDao_revertTopic' => array(
        'description' => '还原帖子时，调用',
        'param'       => array('@param array $tids 帖子tid序列', '@return void'),
        'interface'   => '',
        'list'        => array(
            'threadsIndex' => array(
                'class'   => PwThreadsIndexDao::class,
                'method'  => 'revertTopic',
                'loadway' => 'loadDao',
            ),
            'threadsCateIndex' => array(
                'class'   => PwThreadsCateIndexDao::class,
                'method'  => 'revertTopic',
                'loadway' => 'loadDao',
            ),
            'threadsDigestIndex' => array(
                'class'   => PwThreadsDigestIndexDao::class,
                'method'  => 'revertTopic',
                'loadway' => 'loadDao',
            ),
        ),
    ),
    's_PwThreadsDao_delete' => array(
        'description' => '删除一个帖子时，调用',
        'param'       => array('@param int $id 帖子tid', '@return void'),
        'interface'   => '',
        'list'        => array(
            'threadsIndex' => array(
                'class'   => PwThreadsIndexDao::class,
                'method'  => 'deleteThread',
                'loadway' => 'loadDao',
            ),
            'threadsCateIndex' => array(
                'class'   => PwThreadsCateIndexDao::class,
                'method'  => 'deleteThread',
                'loadway' => 'loadDao',
            ),
            'threadsDigestIndex' => array(
                'class'   => PwThreadsDigestIndexDao::class,
                'method'  => 'deleteThread',
                'loadway' => 'loadDao',
            ),
        ),
    ),
    's_PwThreadsDao_batchDelete' => array(
        'description' => '批量删除多个帖子时，调用',
        'param'       => array('@param array $ids 帖子tid序列', '@return void'),
        'interface'   => '',
        'list'        => array(
            'threadsIndex' => array(
                'class'   => PwThreadsIndexDao::class,
                'method'  => 'batchDeleteThread',
                'loadway' => 'loadDao',
            ),
            'threadsCateIndex' => array(
                'class'   => PwThreadsCateIndexDao::class,
                'method'  => 'batchDeleteThread',
                'loadway' => 'loadDao',
            ),
            'threadsDigestIndex' => array(
                'class'   => PwThreadsDigestIndexDao::class,
                'method'  => 'batchDeleteThread',
                'loadway' => 'loadDao',
            ),
        ),
    ),
    's_addFollow' => array(
        'description' => '当发生关注操作时，调用',
        'param'       => array('@param int $uid 用户', '@param int $touid 被关注用户', '@return void'),
        'interface'   => '',
        'list'        => array(
            'medal' => array(
                'class'   => PwMedalFansDo::class,
                'method'  => 'addFollow',
                'loadway' => 'load',
            ),
            'task' => array(
                'expression' => 'config:site.task.isOpen==1',
                'class'      => PwTaskMemberFansDo::class,
                'method'     => 'addFollow',
                'loadway'    => 'load',
            ),
            'message' => array(
                'class'   => PwNoticeFansDo::class,
                'method'  => 'addFollow',
                'loadway' => 'load',
            ),
        ),
    ),
    's_deleteFollow' => array(
        'description' => '当发生取消关注操作时，调用',
        'param'       => array('@param int $uid 用户', '@param int $touid 被关注用户', '@return void'),
        'interface'   => '',
        'list'        => array(
            'medal' => array(
                'class'   => PwMedalFansDo::class,
                'method'  => 'delFollow',
                'loadway' => 'load',
            ),
            /*
            'recommend' => array(
                'class' => PwRecommendAttentionDo::class,
                'method' => 'delFollow',
                'loadway' => 'load'
            ),*/
        ),
    ),

    's_PwTaskDao_update' => array(
        'description' => '更新一条任务记录时，调用',
        'param'       => array('@param int $id 帖子tid', '@param array $fields 更新的任务字段数据', '@param array $increaseFields 递增的任务字段数据', '@return void'),
        'interface'   => '',
        'list'        => array(
            'TaskUser' => array(
                'class'   => PwTaskUserDao::class,
                'method'  => 'updateIsPeriod',
                'loadway' => 'loadDao',
            ),
        ),
    ),
    's_profile_editUser' => array(
        'description' => '更新用户资料时，调用',
        'param'       => array('@param PwUserInfoDm $dm', '@return void'),
        'interface'   => '',
        'list'        => array(
            'task' => array(
                'expression' => 'config:site.task.isOpen==1',
                'class'      => PwTaskProfileConditionDo::class,
                'loadway'    => 'load',
                'method'     => 'editUser',
            ),
        ),
    ),
    's_update_avatar' => array(
        'description' => '更新用户头像时，调用',
        'param'       => array('@param int $uid 用户id', '@return void'),
        'interface'   => '',
        'list'        => array(
            'task' => array(
                'expression' => 'config:site.task.isOpen==1',
                'class'      => PwTaskMemberAvatarDo::class,
                'loadway'    => 'load',
                'method'     => 'uploadAvatar',
            ),
        ),
    ),
    's_PwUser_delete' => array(
        'description' => '删除用户时，调用',
        'param'       => array('@param int $uid 用户id', '@return void'),
        'interface'   => '',
        'list'        => array(
            'ban' => array(
                'class'   => PwUserDoBan::class,
                'method'  => 'deleteBan',
                'loadway' => 'load',
            ),
            'belong' => array(
                'class'   => PwUserDoBelong::class,
                'method'  => 'deleteUser',
                'loadway' => 'load',
            ),
            'registerCheck' => array(
                'class'   => PwUserDoRegisterCheck::class,
                'method'  => 'deleteUser',
                'loadway' => 'load',
            ),
            'activeCode' => array(
                'class'   => PwUserActiveCode::class,
                'method'  => 'deleteInfoByUid',
                'loadway' => 'load',
            ),
            'task' => array(
                'class'   => PwTaskUser::class,
                'method'  => 'deleteByUid',
                'loadway' => 'load',
            ),
            'usertag' => array(
                'class'   => PwUserTagRelation::class,
                'method'  => 'deleteRelationByUid',
                'loadway' => 'load',
            ),
            'mobile' => array(
                'class'   => PwUserMobile::class,
                'method'  => 'deleteByUid',
                'loadway' => 'load',
            ),
        ),
    ),
    's_PwUser_batchDelete' => array(
        'description' => '批量删除用户时，调用',
        'param'       => array('@param array $uids 用户id序列', '@return void'),
        'interface'   => '',
        'list'        => array(
            'ban' => array(
                'class'   => PwUserDoBan::class,
                'method'  => 'batchDeleteBan',
                'loadway' => 'load',
            ),
            'belong' => array(
                'class'   => PwUserDoBelong::class,
                'method'  => 'batchDeleteUser',
                'loadway' => 'load',
            ),
            'registerCheck' => array(
                'class'   => PwUserDoRegisterCheck::class,
                'method'  => 'batchDeleteUser',
                'loadway' => 'load',
            ),
            'task' => array(
                'class'   => PwTaskUser::class,
                'method'  => 'batchDeleteByUid',
                'loadway' => 'load',
            ),
            'usertag' => array(
                'class'   => PwUserTagRelation::class,
                'method'  => 'batchDeleteRelationByUids',
                'loadway' => 'load',
            ),
        ),
    ),
    's_PwUser_add' => array(
        'description' => '添加用户时，调用',
        'param'       => array('@param PwUserInfoDm $dm', '@return void'),
        'interface'   => '',
        'list'        => array(
            'belong' => array(
                'class'   => PwUserDoBelong::class,
                'method'  => 'editUser',
                'loadway' => 'load',
            ),
        ),
    ),
    's_PwUser_update' => array(
        'description' => '更新用户信息时，调用',
        'param'       => array('@param PwUserInfoDm $dm', '@return void'),
        'interface'   => '',
        'list'        => array(
            'belong' => array(
                'class'   => PwUserDoBelong::class,
                'method'  => 'editUser',
                'loadway' => 'load',
            ),
        ),
    ),
    's_PwUserDataDao_update' => array(
        'description' => '用户数据更新时，调用',
        'param'       => array('@param int $id 用户id', '@param array $fields 更新的用户字段数据', '@param array $increaseFields 递增的用户字段数据', '@return void'),
        'interface'   => '',
        'list'        => array(
            'level' => array(
                'class'   => PwUserGroupsService::class,
                'method'  => 'updateLevel',
                'loadway' => 'load',
            ),
            'autoBan' => array(
                'class'      => PwUserBanService::class,
                'method'     => 'autoBan',
                'loadway'    => 'load',
                'expression' => 'config:site.autoForbidden.open==1',
            ),
        ),
    ),
    's_PwUserGroups_update' => array(
        'description' => '用户组资料更新时，调用',
        'param'       => array('@param int $gid 用户组id', '@return void'),
        'interface'   => '',
        'list'        => array(
            'usergroup' => array(
                'class'   => PwUserGroupsService::class,
                'method'  => 'updateGroupCacheByHook',
                'loadway' => 'load',
            ),
        ),
    ),
    's_PwUserGroupsDao_delete' => array(
        'description' => '删除用户组时，调用',
        'param'       => array('@param int $gid 用户组id', '@return void'),
        'interface'   => '',
        'list'        => array(
            'usergroup' => array(
                'class'   => PwUserGroupsService::class,
                'method'  => 'deleteGroupCacheByHook',
                'loadway' => 'load',
            ),
        ),
    ),
    's_PwUserGroupPermission_update' => array(
        'description' => '用户组权限变更时，调用',
        'param'       => array('@param PwUserPermissionDm $dm', '@return void'),
        'interface'   => '',
        'list'        => array(
            'usergroup_permission' => array(
                'class'   => PwUserGroupsService::class,
                'method'  => 'updatePermissionCacheByHook',
                'loadway' => 'load',
            ),
        ),
    ),
    's_PwLikeService_delLike' => array(
        'description' => '删除喜欢',
        'list'        => array(
            'behavior' => array(
                'class'   => PwMiscLikeDo::class,
                'method'  => 'delLike',
                'loadway' => 'load',
            ),
            'medal' => array(
                'class'   => PwMedalLikeDo::class,
                'method'  => 'delLike',
                'loadway' => 'load',
            ),
        ),
    ),
    's_PwLikeService_addLike' => array(
        'description' => '添加喜欢',
        'list'        => array(
            'task' => array(
                'expression' => 'config:site.task.isOpen==1',
                'class'      => PwTaskBbsLikeDo::class,
                'method'     => 'addLike',
                'loadway'    => 'load',
            ),
            'behavior' => array(
                'class'   => PwMiscLikeDo::class,
                'method'  => 'addLike',
                'loadway' => 'load',
            ),
            'medal' => array(
                'class'   => PwMedalLikeDo::class,
                'method'  => 'addLike',
                'loadway' => 'load',
            ),
        ),
    ),
    's_PwUserTagRelationDao_deleteRelation' => array(
        'description' => '删除用户标签的关系，调用',
        'param'       => array('@param int $tag_id 标签id', '@return void'),
        'interface'   => '',
        'list'        => array(
            'PwUserTag' => array(
                'class'   => PwUserTagDao::class,
                'method'  => 'updateTag',
                'loadway' => 'loadDao',
            ),
        ),
    ),
    's_PwUserTagDao_deleteTag' => array(
        'description' => '删除用户标签时，调用',
        'param'       => array('@param int $tag_id 标签id', '@return void'),
        'interface'   => '',
        'list'        => array(
            'PwUserTagRelation' => array(
                'class'   => PwUserTagRelationDao::class,
                'method'  => 'deleteRelationByTagid',
                'loadway' => 'loadDao',
            ),
        ),
    ),
    's_PwUserTagDao_batchDeleteTag' => array(
        'description' => '批量删除用户标签时，调用',
        'param'       => array('@param array $tag_ids 标签id序列', '@return void'),
        'interface'   => '',
        'list'        => array(
            'PwUserTagRelation' => array(
                'class'   => PwUserTagRelationDao::class,
                'method'  => 'batchDeleteRelationByTagids',
                'loadway' => 'loadDao',
            ),
        ),
    ),
    's_PwUserTagRelation_batchDeleteRelation' => array(
        'description' => '删除用户标签关系的时候',
        'param'       => array('@param array $tag_ids ', '@param PwUserTagRelation ', '@return void'),
        'interface'   => '',
        'list'        => array(
            'PwDeleteRelationDoUpdateTag' => array(
                'class'   => PwDeleteRelationDoUpdateTag::class,
                'method'  => 'batchDeleteRelation',
                'loadway' => 'load',
            ),
        ),
    ),
    's_PwUserTagRelation_deleteRelationByUid' => array(
        'description' => '根据用户ID删除用户标签关系',
        'param'       => array('@param int $uid ', '@return void'),
        'interface'   => '',
        'list'        => array(
            'PwDeleteRelationDoUpdateTag' => array(
                'class'   => PwDeleteRelationDoUpdateTag::class,
                'method'  => 'deleteRelationByUid',
                'loadway' => 'load',
            ),
        ),
    ),
    's_PwUserTagRelation_batchDeleteRelationByUids' => array(
        'description' => '根据用户ID列表批量删除用户标签关系',
        'param'       => array('@param array $uid ', '@return void'),
        'interface'   => '',
        'list'        => array(
            'PwDeleteRelationDoUpdateTag' => array(
                'class'   => PwDeleteRelationDoUpdateTag::class,
                'method'  => 'batchDeleteRelationByUids',
                'loadway' => 'load',
            ),
        ),
    ),
    /*添加表情*/
    's_PwEmotionDao_add' => array(
        'description' => '添加表情时，调用',
        'param'       => array('@param int $id id', '@param array $fields 字段信息', '@return void'),
        'interface'   => '',
        'list'        => array(
            'addEmotion' => array(
                'class'   => PwEmotionService::class,
                'method'  => 'updateCache',
                'loadway' => 'load',
            ),
        ),
    ),
    /*编辑表情*/
    's_PwEmotionDao_update' => array(
        'description' => '编辑表情时，调用',
        'param'       => array('@param int $id 表情id', '@param array $fields 字段信息', '@param array $increaseFields 字段信息', '@return void'),
        'interface'   => '',
        'list'        => array(
            'addEmotion' => array(
                'class'   => PwEmotionService::class,
                'method'  => 'updateCache',
                'loadway' => 'load',
            ),
        ),
    ),
    /*删除表情*/
    's_PwEmotionDao_delete' => array(
        'description' => '删除表情时，调用',
        'param'       => array('@param int $id 表情id', '@return void'),
        'interface'   => '',
        'list'        => array(
            'addEmotion' => array(
                'class'   => PwEmotionService::class,
                'method'  => 'updateCache',
                'loadway' => 'load',
            ),
        ),
    ),
    's_PwEmotionDao_deleteEmotionByCatid' => array(
        'description' => '删除一组表情时，调用',
        'param'       => array('@param int $cateId 表情组id', '@return void'),
        'interface'   => '',
        'list'        => array(
            'addEmotion' => array(
                'class'   => PwEmotionService::class,
                'method'  => 'updateCache',
                'loadway' => 'load',
            ),
        ),
    ),
    's_PwConfigDao_update' => array(
        'description' => '全局配置更新时，调用',
        'param'       => array('@param string $namespace 配置域'),
        'interface'   => '',
        'list'        => array(
            'configCache' => array(
                'class'   => PwConfigService::class,
                'method'  => 'updateConfig',
                'loadway' => 'load',
            ),
        ),
    ),
    's_PwThreadType' => array(
        'description' => '获取帖子扩展类型时，调用',
        'param'       => array('@param array $tType 帖子类型', '@return array'),
        'interface'   => '',
        'list'        => array(),
    ),
    's_punch' => array(
        'description' => '打卡时，调用',
        'param'       => array('@param PwUserInfoDm $dm', '@return void'),
        'interface'   => '',
        'list'        => array(
            'task' => array(
                'expression' => 'config:site.task.isOpen==1',
                'class'      => PwTaskMemberPunchDo::class,
                'method'     => 'doPunch',
                'loadway'    => 'load',
            ),
        ),
    ),
    /*扩展存储类型*/
    's_PwStorage_getStorages' => array( //todo
        'description' => '获取附件存储类型',
        'param'       => array('@param array $storages', '@return array'),
        'interface'   => '',
        'list'        => array(
        ),
    ),
    's_PwThreadManageDoCopy' => array( //todo
        'description' => '帖子复制',
        'param'       => array('@param PwThreadManage $srv', '@return void'),
        'interface'   => 'PwThreadManageCopyDoBase',
        'list'        => array(
            'poll' => array(
                'class'      => PwThreadManageCopyDoPoll::class,
                'method'     => 'copyThread',
                'loadway'    => 'load',
                'expression' => 'service:special==poll',
            ),
            'att' => array(
                'class'      => PwThreadManageCopyDoAtt::class,
                'method'     => 'copyThread',
                'loadway'    => 'load',
                'expression' => 'service:ifupload!=0',
            ),
        ),
    ),
    /* 用户退出之前的更新 */
    's_PwUserService_logout' => array(
        'description' => '退出登录',
        'param'       => array('@param PwUserBo $loginUser', '@return void'),
        'interface'   => 'PwLogoutDoBase',
        'list'        => array(
            'updatelastvist' => array(
                'class'   => PwLogoutDoUpdateLastvisit::class,
                'method'  => 'beforeLogout',
                'loadway' => 'load',
            ),
            'updateOnline' => array(
                'class'   => PwLogoutDoUpdateOnline::class,
                'method'  => 'beforeLogout',
                'loadway' => 'load',
            ),
        ),
    ),
    's_PwEditor_app' => array(
        'description' => '编辑器配置扩展',
        'param'       => array('@param array $var', '@return array'),
        'list'        => array(
        ),
    ),
    's_PwCreditOperationConfig' => array(
        'description' => '积分策略配置',
        'param'       => array('@param array $config 积分策略配置', '@return array'),
        'list'        => array(
        ),
    ),
    's_seo_config' => array(
        'description' => 'seo优化扩展',
        'param'       => array('@param array $config seo扩展配置', '@return array'),
        'list'        => array(
        ),
    ),
    's_PwUserBehaviorDao_replaceInfo' => array(
        'description' => '用户行为更新扩展',
        'param'       => array('@param array $data 用户行为数据', '@return '),
        'list'        => array(
            'task' => array(
                'class'      => PwTaskService::class,
                'method'     => 'sendAutoTask',
                'loadway'    => 'load',
                'expression' => 'config:site.task.isOpen==1',
            ),
        ),
    ),
    's_admin_menu' => array(
        'description' => '后台菜单扩展',
        'param'       => array('@param array $config 后台菜单配置', '@return array'),
        'list'        => array(
        ),
    ),
    's_permissionCategoryConfig' => array(
        'description' => '用户组根权限',
        'param'       => array('@param array $config 用户组根权限', '@return array'),
        'list'        => array(
        ),
    ),
    's_permissionConfig' => array(
        'description' => '用户组权限',
        'param'       => array('@param array $config 用户组权限', '@return array'),
        'list'        => array(
        ),
    ),
    's_PwMobileService_checkVerify' => array(
        'description' => '验证手机完成',
        'param'       => array('@param int $mobile'),
        'list'        => array(
        ),
    ),
    's_header_nav' => array(
        'description' => '全局头部导航',
        'param'       => array(),
        'list'        => array(
        ),
    ),
    's_header_info_1' => array(
        'description' => '头部用户信息扩展点1',
        'param'       => array(),
        'list'        => array(
        ),
    ),
    's_header_info_2' => array(
        'description' => '头部用户信息扩展点2',
        'param'       => array(),
        'list'        => array(
        ),
    ),
    's_header_my' => array(
        'description' => '头部帐号的下拉',
        'param'       => array(),
        'list'        => array(
        ),
    ),
    's_footer' => array(
        'description' => '全局底部',
        'param'       => array(),
        'list'        => array(
        ),
    ),
    's_space_nav' => array(
        'description' => '个人空间导航扩展',
        'param'       => array('@param array $space', '@param string $src'),
        'list'        => array(
        ),
    ),
    's_space_profile' => array(
        'description' => '空间资料页面',
        'param'       => array('@param array $space'),
        'interface'   => '',
        'list'        => array( //这个顺序别改，pd要求的
            'education' => array(
                'class'  => PwSpaceProfileDoEducation::class,
                'method' => 'createHtml',
            ),
            'work' => array(
                'class'  => PwSpaceProfileDoWork::class,
                'method' => 'createHtml',
            ),
        ),
    ),
    's_profile_menus' => array(
        'description' => '个人设置-菜单项扩展',
        'param'       => array('@param array $config 注册的菜单', '@return array'),
        'list'        => array(
        ),
    ),

    's_attachment_watermark' => array(
        'description' => '全局->水印设置->水印策略扩展',
        'param'       => array('@param array $config 已有的需要设置的策略,每一个扩展项格式:key=>title', '@return array'),
        'list'        => array(
        ),
    ),
    's_verify_showverify' => array(
        'description' => '全局->验证码->验证策略',
        'param'       => array('@param array $config 需要设置的策略,每一个扩展项格式:key=>title', '@return array'),
        'list'        => array(
        ),
    ),
    /*手机短信扩展*/
    's_PwMobileService_getPlats' => array(
        'description' => '手机短信 - 平台选择',
        'param'       => array('@param array $config 配置文件，可参考SRV:mobile.config.plat.php', '@return array'),
        'list'        => array(
        ),
    ),
);
