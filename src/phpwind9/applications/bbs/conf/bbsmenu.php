<?php

return [
    'bbs'           => ['论坛', []],
    'bbs_configbbs' => ['论坛设置', 'bbs/configbbs/*', '', '', 'bbs'],
    'bbs_setforum'  => ['版块管理', 'bbs/setforum/*', '', '', 'bbs'],
    'bbs_setbbs'    => ['功能细节', 'bbs/setbbs/*', '', '', 'bbs'],

    'bbs_article' => ['帖子管理', 'bbs/article/*', '', '', 'contents'],

    //'bbs_contentcheck' => array('内容审核', 'bbs/contentcheck/*', '', '', 'contents'),
    'bbs_contentcheck'       => ['内容审核', [], '', '', 'contents'],
    'bbs_contentcheck_forum' => ['帖子审核', 'bbs/contentcheck/*', '', '', 'bbs_contentcheck'],

    'bbs_recycle' => ['回收站', 'bbs/recycle/*', '', '', 'contents'],
];
