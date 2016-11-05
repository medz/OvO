<?php
Wind::import('ADMIN:library.AdminBaseController');

/**
 * 会员相关-完成条件扩展
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: TaskConditionMemberController.php 15745 2012-08-13 02:45:07Z xiaoxia.xuxx $
 * @package src.modules.task.admin
 */
class TaskConditionMemberController extends AdminBaseController{
	
	/* (non-PHPdoc)
	 * @see AdminBaseController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		$var = unserialize($this->getInput('var'));
		if (is_array($var)) {
			$this->setOutput($var, 'condition');
		}
	}
	
	/**
	 * 完成资料
	 */
	public function profileAction() {
		$this->setTemplate('condition.member_profile');
	}
	
	/**
	 * 上传头像
	 */
	public function avatarAction() {
		$this->setTemplate('condition.member_avatar');
	}
	
	/**
	 * 发送消息
	 */
	public function sendMsgAction() {
		$this->setTemplate('condition.member_msg');
	}
	
	/**
	 * 打卡
	 */
	public function punchAction() {
		$this->setTemplate('condition.member_punch');
	}
	
	/**
	 * 求粉丝
	 */
	public function fansAction() {
		$this->setTemplate('condition.member_fans');
	}
}