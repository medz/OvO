<?php
Wind::import('LIB:process.iPwGleanDoHookProcess');

/**
 * 清理用户消息
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwClearDoMessage.php 16415 2012-08-23 07:53:40Z xiaoxia.xuxx $
 * @package src.hooks.PwClearUser
 */
class PwClearDoMessage extends iPwGleanDoHookProcess {
	/* @var $operator PwUserBo */
	private $operator = null;
	
	/* (non-PHPdoc)
	 * @see iPwGleanDoHookProcess::run()
	 */
	public function run($uid) {
		/* @var $srv PwMessageService */
		$srv = Wekit::load('SRV:message.srv.PwMessageService');
		$srv->deleteUserMessages($uid, true, true);
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