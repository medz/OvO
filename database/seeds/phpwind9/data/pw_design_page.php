<?php

// INSERT INTO `pw_design_page` (`page_id`, `page_type`, `page_name`, `page_router`, `page_unique`, `is_unique`, `module_ids`, `struct_names`, `segments`, `design_lock`) VALUES

return [
    [1, 2, '版块列表首页', 'bbs/forumlist/run', 0, 0, ',', '', '', ''],
    [2, 2, '论坛新贴', 'bbs/index/run', 0, 0, ',', '', '', ''],
    [3, 2, '版块列表页', 'bbs/thread/run', 1, 0, ',', '', '', ''],
    [4, 2, '帖子阅读页', 'bbs/read/run', 1, 0, ',', '', '', ''],
    [5, 2, '论坛分类页', 'bbs/cate/run', 1, 0, ',', '', '', ''],

];
