<?php
Wind::import('LIB:process.iPwGleanDoHookProcess');
Wind::import('SRV:forum.srv.dataSource.PwFetchReplyByUid');
Wind::import('SRV:forum.srv.operation.PwDeleteReply');


/**
 * 清除用户数据----帖子回复
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwClearDoPost.php 16415 2012-08-23 07:53:40Z xiaoxia.xuxx $
 * @package src.hooks.PwClearUser
 */
class PwClearDoPost extends iPwGleanDoHookProcess {
	/* @var $operator PwUserBo */
	private $operator = null;
	
	/* (non-PHPdoc)
	 * @see iPwGleanDoHookProcess::run()
	 */
	public function run($uid) {
		$operator = new PwDeleteReply(new PwFetchReplyByUid($uid), $this->operator);
		$operator->execute();
	}
	
	/* (non-PHPdoc)
	 * @see iPwGleanDoHookProcess::gleanData()
	*/
	public function gleanData($bo) {
		$this->operator = $bo;
		return true;
	}
}