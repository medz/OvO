<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:weibo.dm.PwWeiboCommnetDm');

/**
 * 新鲜事回复
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwFreshReplyByWeibo.php 22521 2012-12-25 07:06:32Z jinlong.panjl $
 * @package src.service.user.srv
 */

class PwFreshReplyByWeibo {
	
	protected $dm;
	protected $user;

	protected $isTransmit;
	protected $newId = 0;
	private static $_hookInstance = null;

	public function __construct($fresh, PwUserBo $user) {
		$this->user = $user;
		$this->dm = new PwWeiboCommnetDm();
		$this->dm->setWeiboId($fresh['src_id']);
	}

	public function check() {
		return true;
	}
	
	public function setContent($content) {
		$this->dm->setContent($content);
	}

	public function setIsTransmit($isTransmit) {
		$this->isTransmit = $isTransmit;
	}

	public function execute() {
		$this->dm->setCreatedUser($this->user->uid, $this->user->username);
		$this->dm->setCreatedTime(Pw::getTime());
		if (($result = $this->_getHook()->runWithVerified('check', $this->dm)) instanceof PwError) {
			return $result;
		}
		$result = Wekit::load('weibo.srv.PwWeiboService')->addComment($this->dm, $this->user);
		if ($result instanceof PwError) {
			return $result;
		}
		if ($this->isTransmit) {
			Wind::import('SRV:weibo.srv.PwSendWeibo');
			$dm2 = new PwWeiboDm();
			$dm2->setContent($this->dm->getField('content'));
			$dm2->setSrcId($this->dm->getField('weibo_id'));
			$sendweibo = new PwSendWeibo($this->user);
			$this->newId = $sendweibo->send($dm2);
		}
		if (($result = $this->_getHook()->runDo('addWeibo', $this->dm)) instanceof PwError) {
			return $result;
		}
		return true;
	}

	public function getIscheck() {
		return 1;
	}

	public function getIsuseubb() {
		return strpos($this->dm->getField('content'), '[s:') !== false ? 1 : 0;
	}

	public function getRemindUser() {
		return array();
	}

	public function getNewFreshSrcId() {
		return $this->newId;
	}
	
	/**
	 * 
	 * @return PwHookService
	 */
 	private function _getHook() {
 		if (self::$_hookInstance == null) {
			self::$_hookInstance = new PwHookService('PwFreshReplyByWeibo', 'PwWeiboDoBase');
		}
 		return self::$_hookInstance;
 	}
}