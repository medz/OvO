<?php

/**
 * windid用户信息数据模型
 * 
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com> 2010-11-2
 * @license http://www.phpwind.com
 * @version $Id: PwCreditDm.php 24707 2013-02-16 06:39:01Z jieyin $
 * @package windid.service.user.dm
 */
class PwCreditDm {

	public $dm = null;
	public $uid;
	protected $_data = array();
	protected $_increaseData = array();
	
	public function __construct($uid) {
		$this->uid = $uid;
	}
	
	public function getDm() {
		if (!is_object($this->dm)) {
			$dm = WindidApi::getDm('credit');
			$this->dm = new $dm($this->uid);
		}
		return $this->dm;
	}
	
	public function getData() {
		return $this->_data;
	}
	
	public function addCredit($cType, $value) {
		$this->getDm()->addCredit($cType, $value);
		return $this;
	}

	public function setCredit($cType, $value) {
		$this->getDm()->setCredit($cType, $value);
		return $this;
	}
	
	/**
	 * 积分字段合法性检查
	 *
	 * @param int $key
	 * @return boolean
	 */
	private function _isLegal(&$key) {
		$key = intval($key);
		return $key >= 1;
	}

}