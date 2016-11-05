<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:process.iPwGleanDoHookProcess');

/**
 * 删除帖子精华信息---进入回收站的帖子同时删除该帖子的精华信息
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwDeleteTopicDoDigestDelete.php 15975 2012-08-16 09:40:09Z xiaoxia.xuxx $
 * @package src.hooks.PwDeleteTopic
 */
class PwDeleteTopicDoDigestDelete extends iPwGleanDoHookProcess{
	
	/* (non-PHPdoc)
	 * @see iPwGleanDoHookProcess::gleanData()
	 */
	public function gleanData($value) {
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see iPwGleanDoHookProcess::run()
	 */
	public function run($ids) {
		Wekit::load('forum.PwThreadDigestIndex')->batchDeleteThread($ids);
		return true;
	}
}