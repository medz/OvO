<?php 
return array(
	'bbs' => array('论坛', array()),
	'bbs_configbbs' => array('论坛设置', 'bbs/configbbs/*', '', '', 'bbs'),
	'bbs_setforum' => array('版块管理', 'bbs/setforum/*', '', '', 'bbs'),
	'bbs_setbbs' => array('功能细节', 'bbs/setbbs/*', '', '', 'bbs'),

	'bbs_article' => array('帖子管理', 'bbs/article/*', '', '', 'contents'),

	//'bbs_contentcheck' => array('内容审核', 'bbs/contentcheck/*', '', '', 'contents'),
	'bbs_contentcheck' => array('内容审核', array(), '', '', 'contents'),
	'bbs_contentcheck_forum' => array('帖子审核', 'bbs/contentcheck/*', '', '', 'bbs_contentcheck'),

	'bbs_recycle' => array('回收站', 'bbs/recycle/*', '', '', 'contents'),
);