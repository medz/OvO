<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 积分服务
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwCreditSetService.php 24718 2013-02-17 06:42:06Z jieyin $
 * @package src.service.credit
 */
class PwCreditSetService {
	
	/**
	 * 获得设置的积分选项
	 *
	 * @return array
	 */
	public function getCredit() {
		return $this->_getWindid()->get('credit:credits');
	}

	/** 
	 * 设置用户积分
	 * 
	 * @param array $credit 积分配置信息<array('1' => array('name'=>?,'unit'=>?,'descrip'=>?), '2' => ?, ...)>
	 * @param array $new 新增加的积分
	 * @return boolean
	 */
	public function setCredits($credits, $newCredit = array()) {
		is_array($credits) || $credits = array();
		if ($newCredit) {
			$keys = array_keys($credits);
			$maxKey = intval(max($keys));
			$range = range(1, $maxKey + count($newCredit));
			$freeKeys = array_diff($range, $keys);
			asort($freeKeys);

			foreach ($newCredit as $key => $value) {
				if (!$value['name']) continue;
				$_key = array_shift($freeKeys);
				$credits[$_key] = $value;
			}
		}
		$this->_getWindid()->setCredits($this->_getWindidCredits($credits));
		$this->setLocalCredits($credits);
		return true;
	}
	
	/**
	 * 设置本地积分配置
	 *
	 * @param array $credits
	 * @return bool
	 */
	public function setLocalCredits($credits) {
		$struct = $this->_getDs()->getCreditStruct();
		foreach ($credits as $key => $value) {
			if (!in_array('credit' . $key, $struct)) {
				$this->_getDs()->alterAddCredit($key);
			}
		}
		foreach ($struct as $key => $value) {
			$_key = substr($value, 6);
			if (!isset($credits[$_key])) {
				if ($_key < 9) {
					$this->_getDs()->clearCredit($_key);
				} else {
					$this->_getDs()->alterDropCredit($_key);
				}
			}
		}
		$config = new PwConfigSet('credit');
		$config->set('credits', $credits)->flush();
		return true;
	}

	/** 
	 * 删除积分
	 *
	 * @param int $creditId 积分ID
	 * @return PwError|boolean
	 */
	public function deleteCredit($creditId) {
		if ($creditId < 0) {
			return new PwError("User:deleteCredit.illegal.creditId");
		}
		
		$creditConfig = Wekit::C()->getConfigByName('credit', 'credits');
		$credits = unserialize($creditConfig['value']);
		unset($credits[$creditId]);
		
		$this->_getWindid()->setCredits($this->_getWindidCredits($credits));
		$this->setLocalCredits($credits);
		return true;
	}

	protected function _getWindidCredits($credits) {
		$wcredits = array();
		foreach ($credits as $key => $value) {
			$wcredits[$key] = array('name' => $value['name'], 'unit' => $value['unit']);
		}
		return $wcredits;
	}
	
	/**
	 * 获取DS
	 *
	 * @return PwUserDataExpand
	 */
	private function _getDs() {
		return Wekit::load('user.PwUserDataExpand');
	}

	private function _getWindid() {
		return WindidApi::api('config');
	}
}