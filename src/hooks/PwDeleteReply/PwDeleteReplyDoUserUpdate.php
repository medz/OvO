<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:process.iPwGleanDoHookProcess');
Wind::import('SRV:credit.bo.PwCreditBo');
Wind::import('SRV:user.dm.PwUserInfoDm');

/**
 * 帖子删除扩展服务接口--更新用户发帖数，积分等信息
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDeleteReplyDoUserUpdate.php 17512 2012-09-06 04:50:49Z xiaoxia.xuxx $
 * @package forum
 */

class PwDeleteReplyDoUserUpdate extends iPwGleanDoHookProcess {
	
	public $record = array();
	
	/* (non-PHPdoc)
	 * @see iPwGleanDoHookProcess::gleanData()
	 */
	public function gleanData($value) {
		if ($value['disabled'] != 2) {
			$this->record[$value['created_userid']]++;
			if ($this->srv->isDeductCredit) {
				$this->_operateCredit($value);
			}
		}
	}
	
	/**
	 * 积分操作
	 * 
	 * @param array $value 回帖
	 */
	protected function _operateCredit($value) {
		Wind::import('SRV:forum.bo.PwForumBo');
		$forum = new PwForumBo($value['fid']);
		PwCreditBo::getInstance()->operate(
			'delete_reply', PwUserBo::getInstance($value['created_userid']), true, array(
				'operator' => $this->srv->user->username,
				'forumname' => $forum->foruminfo['name'],
			), 
			$forum->getCreditSet('delete_reply'));
	}
	
	public function run($ids) {
		if ($this->record) {
			foreach ($this->record as $key => $value) {
				$dm = new PwUserInfoDm($key);
				$dm->addPostnum(-$value);
				Wekit::load('user.PwUser')->editUser($dm, PwUser::FETCH_DATA);
			}
			PwCreditBo::getInstance()->execute();
		}
	}
}