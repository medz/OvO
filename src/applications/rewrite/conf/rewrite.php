<?php
/**
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
return array(
	/*'别名' => array('名称', '可用url参数', 'm/c/a', '默认url格式')*/
	'default' => array('默认规则', '', '', ''), 
	'thread' => array('论坛帖子列表页', '{fid}{page}{fname}', 'bbs/thread/run', 'thread-{fid}-{page}'), 
	'read' => array('论坛帖子阅读页', '{tid}{page}{fid}{fname}', 'bbs/read/run', 'read-{tid}-{page}'), 
	'special' => array('门户页面', '{id}', 'special/index/run', 'special-{id}'),
	'space' => array('个人空间', '{uid}', 'space/index/run', 'u-{uid}'),
	'tag' => array('话题浏览页', '{name}', 'tag/index/view', 'tag-{name}'),
);