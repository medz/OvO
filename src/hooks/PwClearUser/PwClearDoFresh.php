<?php
Wind::import('LIB:process.iPwGleanDoHookProcess');
Wind::import('SRV:forum.srv.dataSource.PwFetchReplyByUid');
Wind::import('SRV:forum.srv.operation.PwDeleteReply');

/**
 * 删除新鲜事
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwClearDoFresh.php 16415 2012-08-23 07:53:40Z xiaoxia.xuxx $
 * @package src.hooks.PwClearUser
 */
class PwClearDoFresh extends iPwGleanDoHookProcess {
	/* @var $operator PwUserBo */
	private $operator = null;
	
	/* (non-PHPdoc)
	 * @see iPwGleanDoHookProcess::run()
	 */
	public function run($uid) {
		//TODO 清除用户的新鲜事
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see iPwGleanDoHookProcess::gleanData()
	 */
	public function gleanData($bo) {
		$this->operator = $bo;
		return true;
	}
}