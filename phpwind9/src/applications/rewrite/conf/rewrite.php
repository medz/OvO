<?php
/**
 * @author Shi Long <long.shi@alibaba-inc.com>
 *
 * @link http://www.phpwind.com
 *
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
return [
    /*'别名' => array('名称', '可用url参数', 'm/c/a', '默认url格式')*/
    'default' => ['默认规则', '', '', ''],
    'thread'  => ['论坛帖子列表页', '{fid}{page}{fname}', 'bbs/thread/run', 'thread-{fid}-{page}'],
    'read'    => ['论坛帖子阅读页', '{tid}{page}{fid}{fname}', 'bbs/read/run', 'read-{tid}-{page}'],
    'special' => ['门户页面', '{id}', 'special/index/run', 'special-{id}'],
    'space'   => ['个人空间', '{uid}', 'space/index/run', 'u-{uid}'],
    'tag'     => ['话题浏览页', '{name}', 'tag/index/view', 'tag-{name}'],
];
