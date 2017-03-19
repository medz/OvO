<?php

defined('WEKIT_VERSION') or exit(403);
/*
 * 全局配置
 */
return [

/**=====配置开始于此=====**/

/*-----预设缓存键值-----*/

'precache' => [
    'default/index/run' => [
        ['hot_tags', [0, 10]], 'medal_auto', 'medal_all',
    ],
    'bbs/index/run' => [
        ['hot_tags', [0, 10]], 'medal_auto', 'medal_all',
    ],
    'bbs/forum/run' => [
        ['hot_tags', [0, 10]],
    ],
    'bbs/cate/run' => [
        ['hot_tags', [0, 10]],
    ],
    'bbs/thread/run' => [
        ['hot_tags', [0, 10]], 'medal_auto', 'medal_all',
    ],
    'bbs/read/run' => ['level', 'group_right', 'medal_all'],
],

/*-----预设钩子键值-----*/

'prehook' => [
    'ALL'     => ['s_head', 's_header_nav', 's_footer'],
    'LOGIN'   => ['s_header_info_1', 's_header_info_2', 's_header_my'],
    'UNLOGIN' => ['s_header_info_3'],

    'default/index/run' => ['c_index_run', 'm_PwThreadList'],
    'bbs/index/run'     => ['c_index_run', 'm_PwThreadList'],
    'bbs/cate/run'      => ['c_cate_run', 'm_PwThreadList'],
    'bbs/thread/run'    => ['c_thread_run', 'm_PwThreadList', 's_PwThreadType'],
    'bbs/read/run'      => ['c_read_run', 'm_PwThreadDisplay', 's_PwThreadType', 's_PwUbbCode_convert', 's_PwThreadsHitsDao_add'],
    'bbs/post/doadd'    => ['c_post_doadd', 'm_PwTopicPost', 's_PwThreadsDao_add', 's_PwThreadsIndexDao_add', 's_PwThreadsCateIndexDao_add', 's_PwThreadsContentDao_add', 's_PwForumStatisticsDao_update', 's_PwForumStatisticsDao_batchUpdate', 's_PwTagRecordDao_add', 's_PwTagRelationDao_add', 's_PwTagDao_update', 's_PwTagDao_add', 's_PwThreadsContentDao_update', 's_PwFreshDao_add', 's_PwUserDataDao_update', 's_PwUser_update', 's_PwAttachDao_update', 's_PwThreadAttachDao_update', 's_PwCreditOperationConfig'],
    'bbs/post/doreply'  => ['c_post_doreply', 'm_PwReplyPost', 's_PwPostsDao_add', 's_PwForumStatisticsDao_update', 's_PwForumStatisticsDao_batchUpdate', 's_PwThreadsDao_update', 's_PwThreadsIndexDao_update', 's_PwThreadsCateIndexDao_update', 's_PwThreadsDigestIndexDao_update', 's_PwUserDataDao_update', 's_PwUser_update', 's_PwCreditOperationConfig'],
    'u/login/dorun'     => ['c_login_dorun', 's_PwUserDataDao_update', 's_PwUser_update', 'm_PwLoginService'],
    'u/login/welcome'   => ['s_PwUserDataDao_update', 's_PwUser_update', 'm_PwLoginService', 's_PwCronDao_update'],
    'u/register/dorun'  => ['c_register', 'm_PwRegisterService'],
],

/*-----缓存用到的key-----*/

'cacheKeys' => [
    'config'        => ['config', [], PwCache::USE_FILE, 'default', 0, ['cache.srv.PwCacheUpdateService', 'getConfigCacheValue']],
    'level'         => ['level', [], PwCache::USE_ALL, 'default', 0, ['usergroup.srv.PwUserGroupsService', 'getLevelCacheValue']],
    'group'         => ['group_%s', ['gid'], PwCache::USE_ALL, 'default', 0, ['usergroup.srv.PwUserGroupsService', 'getGroupCacheValueByGid']],
    'group_right'   => ['group_right', [], PwCache::USE_ALL, 'default', 0, ['usergroup.srv.PwUserGroupsService', 'getGroupRightCacheValue']],
    'hot_tags'      => ['hot_tags_%s_%s', ['cateid', 'num'], PwCache::USE_ALL, 'default', 3600, ['tag.srv.PwTagService', 'getHotTagsNoCache']],
    'medal_all'     => ['medal_all', [], PwCache::USE_ALL, 'default', 0, ['medal.srv.PwMedalService', 'getMedalAllCacheValue']],
    'medal_auto'    => ['medal_auto', [], PwCache::USE_ALL, 'default', 0, ['medal.srv.PwMedalService', 'getMedalAutoCacheValue']],
    'all_emotions'  => ['all_emotions', [], PwCache::USE_ALL, 'default', 0, ['emotion.srv.PwEmotionService', 'getAllEmotionNoCache']],
    'word'          => ['word', [], PwCache::USE_FILE, 'default', 0, [PwWordFilter::class, 'fetchAllWordNoCache']],
    'word_replace'  => ['word_replace', [], PwCache::USE_FILE, 'default', 0, [PwWordFilter::class, 'getReplaceWordNoCache']],
    'advertisement' => ['advertisement', [], PwCache::USE_ALL, 'default', 0, [PwAdService::class, 'getInstalledPosition']],
],

/**=====配置结束于此=====**/
];
