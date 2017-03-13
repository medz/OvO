<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: router.php 23472 2013-01-10 04:08:29Z gao.wanggao $
 */
return [
            'default/index/run' => ['网站首页'],
            'bbs/read/run'      => ['帖子阅读页', 'tid'],
            'bbs/thread/run'    => ['版块列表页', 'fid'],
            'bbs/index/run'     => ['论坛新贴'],
            'bbs/cate/run'      => ['论坛分类页', 'fid'],
            //'bbs/thread/run/digest'	=>array('版块精华', 'fid'),
            'bbs/forum/my'      => ['我的版块页'],
            'bbs/forum/list'    => ['版块列表页'],
            'my/fresh/run'      => ['我的关注'],
            'bbs/forumlist/run' => ['版块列表首页'],
            'special/index/run' => ['', 'id'],
            'like/like/run'     => ['热门喜欢'],
            'like/like/ta'      => ['Ta的喜欢'],
            'like/mylike/run'   => ['我的喜欢'],
            'tag/index/run'     => ['热门话题'],
            'tag/index/my'      => ['我的话题'],
        ];
