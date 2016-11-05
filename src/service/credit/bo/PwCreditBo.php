<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:credit.dm.PwCreditDm');
Wind::import('SRV:credit.dm.PwCreditLogDm');
Wind::import('SRV:credit.srv.PwCreditOperationConfig');

/**
 * 积分对象 (单列对象-通过方法 getInstance 获取)
 *
 * @author JianMin Chen <sky_hold@163.com> 2011-12-19
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwCreditBo.php 22230 2012-12-19 21:45:20Z xiaoxia.xuxx $
 * @package src.service.credit
 */

class PwCreditBo {

	public $cType = array();
	public $cUnit = array();
	public $isLog = array();
	
	private $_set = array();
	private $_get = array();
	private $_log = array();
	private $_num = array();
	private $_userLog = array();
	private $_ops = array();
	private static $_instance = null;

	private function __construct() {
		$credits = Wekit::C('credit', 'credits');
		foreach ($credits as $key => $value) {
			if (!$value['open']) continue;
			$this->cType[$key] = $value['name'];
			$this->cUnit[$key] = $value['unit'];
			$this->isLog[$key] = $value['log'];
		}
	}

	public static function getInstance() {
		isset(self::$_instance) || self::$_instance = new self();
		return self::$_instance;
	}

	/**
	 * 获取积分设置
	 *
	 * @param string $key
	 * @param int $gid 用户组id
	 * @return array
	 */
	public function getStrategy($key) {
		$strategy = Wekit::C('credit', 'strategy');
		return isset($strategy[$key]) ? $strategy[$key] : array();
	}
	
	/**
	 * 设置用户积分(+-)
	 *
	 * @param int $uid 用户UID
	 * @param int $cType 积分类型
	 * @param int $point +-的值
	 * @param bool $delay 是否实时进行数据库操作
	 * @return bool
	 */
	public function set($uid, $cType, $point, $delay = false) {
		if (!isset($this->cType[$cType]) || empty($point)) {
			return false;
		}
		$arr = array(
			$uid => array($cType => $point)
		);
		if ($delay) {
			$this->_append($arr);
		} else {
			$this->execute($arr);
		}
		return true;
	}
	
	/**
	 * 设置用户多个积分(+-)
	 *
	 * @param int $uid 用户UID
	 * @param array $setv 积分值 array('1' => ??, '2' => ??, ...)
	 * @param bool $delay 是否实时进行数据库操作
	 * @return bool
	 */
	public function sets($uid, $setv, $delay = false) {
		if (empty($setv) || !is_array($setv)) {
			return false;
		}
		if ($delay) {
			$this->_append(array($uid => $setv));
		} else {
			$this->execute(array($uid => $setv));
		}
		return true;
	}
	
	/**
	 * 设置多个用户多个积分(+-)
	 *
	 * @param array $uids 用户UID array(1, 2, 3, ...)
	 * @param array $setv 积分值 array('1' => ??, '2' => ??, ...)
	 * @param bool $delay 是否实时进行数据库操作
	 * @return bool
	 */
	public function setus($uids, $setv, $delay = false) {
		if (empty($uids) || !is_array($uids) || empty($setv) || !is_array($setv)) {
			return false;
		}
		$arr = array();
		foreach ($uids as $uid) {
			$arr[$uid] = $setv;
		}
		if ($delay) {
			$this->_append($arr);
		} else {
			$this->execute($arr);
		}
		return true;
	}
	
	/**
	 * 积分操作
	 *
	 * @param string $operation 积分相关操作,根据后台设置
	 * @param object $user 被操作用户
	 * @param bool $delay 是否实时进行数据库操作
	 * @param array $log 日志信息描述
	 * @param array $creditset 特殊的积分设置
	 * @return bool
	 */
	public function operate($operation, PwUserBo $user, $delay = false, $log = array(), $creditset = array()) {
		$strategy = $this->getStrategy($operation);
		if (!$strategy && !$creditset) {
			return false;
		}
		//如果外部有积分设置传入则使用外部的积分设置策略
		if (!empty($creditset['limit']) || ($creditset['credit'] && false === $this->_checkCreditSetEmpty($creditset['credit']))) {
			$strategy['limit'] = $creditset['limit'];
			$strategy['credit'] = $creditset['credit'];
		}
		if ($strategy['limit']) {
			$count = $this->getOperateCount($user->uid, $operation);
			if ($count >= $strategy['limit']) return false;
			$this->_num[$user->uid][$operation] = ++$count;
		}
		$this->addLog($operation, $strategy['credit'], $user, $log);
		return $this->sets($user->uid, $strategy['credit'], $delay);
	}

	/**
	 * 对给定数据进行数据库积分增减操作
	 *
	 * @param array $arr 操作数据 array(1 => array('1' => ??, '2' => ??), 2 => array(), 3 => array(), ...)
	 * @param bool $isAdd true,增加操作|false,设置操作
	 * @return void
	 */
	public function execute($arr = array(), $isAdd = true) {
		if (empty($arr)) {
			$arr = $this->_set;
			$this->_set = array();
		}
		$method = $isAdd ? 'addCredit' : 'setCredit';
		$service = Wekit::load('user.PwUser');
		foreach ($arr as $uid => $setv) {
			$dm = new PwCreditDm($uid);
			foreach ($setv as $cid => $v) {
				if (isset($this->cType[$cid])) $dm->$method($cid, $v);
			}
			$service->updateCredit($dm);
		}
		$this->writeLog();
	}
	
	/**
	 * 记录积分日志
	 *
	 * @param string $operation 操作类型
	 * @param array $setv 积分值 array('1' => ??, '2' => ??, ...)
	 * @param object $user 被操作用户
	 * @param array $log 日志信息描述
	 * @return void
	 */
	public function addLog($operation, $setv, PwUserBo $user, $log = array()) {
		if (!is_array($setv) || !$setv) return false;
		$log['uid'] = $user->uid;
		$log['username'] = $user->username;
		$coc = PwCreditOperationConfig::getInstance();
		$_creditAffect = array();
		foreach ($setv as $key => $affect) {
			if (isset($this->cType[$key]) && $this->isLog[$key] && $affect <> 0) {
				$log['cname'] = $this->cType[$key];
				$log['affect'] = $affect > 0 ? '+' . $affect : $affect;
				$descrip = $coc->getDescrip($operation, $log);
				$dm = new PwCreditLogDm();
				$dm->setCtype($key)
					->setAffect($affect)
					->setLogtype($operation)
					->setDescrip($descrip)
					->setCreatedUser($user->uid, $user->username)
					->setCreatedTime(Pw::getTime());
				$this->_log[] = $dm;
				$_creditAffect[] = array($log['cname'], $log['affect']);
			}
		}
		//TODO 记录用户的积分变动情况---
		//change: judge if the operate is in the "global->credit->strategy"
		//exists: add user credit log
		if ($coc->isCreditPop($operation) && $_creditAffect) {
			$this->_userLog[$user->uid] = array($coc->getName($operation), $_creditAffect);
		}
	}

	public function writeLog() {
		if (!empty($this->_log)) {
			Wekit::load('credit.PwCreditLog')->batchAdd($this->_log);
		}
		if (!empty($this->_num)) {
			$tmp = array();
			$t = Pw::getTime();
			foreach ($this->_num as $uid => $ops) {
				foreach ($ops as $op => $n) {
					$tmp[] = array($uid, $op, $n, $t);
				}
			}
			Wekit::load('credit.PwCreditLog')->batchAddOperate($tmp);
		}
		
		//TODO 记录用户的积分变动情况--用户表字段last_credit_affect_log
		if (!empty($this->_userLog)) {
			/* @var $userDs PwUser */
			$userDs = Wekit::load('user.PwUser');
			Wind::import('SRV:user.dm.PwUserInfoDm');
			foreach ($this->_userLog as $_uid => $_log) {
				$_dm = new PwUserInfoDm($_uid);
				$_dm->setLastCreditAffectLog(serialize($_log));
				$userDs->editUser($_dm, PwUser::FETCH_DATA);
			}
		}
		$this->_userLog = array();
		$this->_log = array();
		$this->_num = array();
	}

	public function getOperateCount($uid, $operate) {
		if (isset($this->_num[$uid][$operate])) return $this->_num[$uid][$operate];
		isset($this->_ops[$uid]) || $this->_ops[$uid] = Wekit::load('credit.PwCreditLog')->getOperate($uid);
		$ops = $this->_ops[$uid];
		if (isset($ops[$operate]) && $ops[$operate]['update_time'] > Pw::getTdtime()) {
			return $ops[$operate]['num'];
		}
		return 0;
	}

	public function mergeCreditSet($set1, $set2) {
		foreach ($set1 as $key => $value) {
			isset($set2[$key]) && $set2[$key] !== '' && $value = $set2[$key];
			$set1[$key] = $value;
		}
		return $set1;
	}
	
	/**
	 * 检测积分是为空
	 *
	 * @param array $credit
	 * @return boolean
	 */
	private function _checkCreditSetEmpty($credit) {
		$empty = true;
		foreach ($credit as $key => $value) {
			if ($value) return false;
		}
		return $empty;
	}

	private function _append($arr) {
		foreach ($arr as $uid => $setv) {
			foreach ($setv as $key => $value) {
				if (isset($this->cType[$key]) && is_numeric($value) && $value <> 0) {
					$this->_set[$uid][$key] += $value;
					//isset($this->getUser[$uid][$key]) && $this->getUser[$uid][$key] += $value;
				}
			}
		}
	}
}