<?php
/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com/license.php
 * @version $Id$
 * @package wind
 */
class PwApplicationLog {

	/**
	 * 添加日志
	 *
	 * @param array $fields
	 * @return PwError|Ambigous <boolean, Ambigous, rowCount, number>
	 */
	public function add($appId, $logType, $data) {
		if (!$appId || !$logType) return new PwError('APPCENTER:validate.fail');
		$fields = array(
			'app_id' => $appId, 
			'log_type' => $logType, 
			'data' => serialize($data), 
			'created_time' => Pw::getTime(), 
			'modified_time' => Pw::getTime());
		return $this->_load()->add($fields);
	}

	/**
	 * 批量添加日志
	 *
	 * @param array $fields
	 * @return PwError|Ambigous <Ambigous, rowCount, boolean, number>
	 */
	public function batchAdd($fields) {
		if (!$fields) return new PwError('APPCENTER:validate.fail');
		foreach ($fields as $key => $value) {
			if (!isset($value['data'])) continue;
			$fields[$key]['data'] = serialize($value['data']);
		}
		return $this->_load()->batchAdd($fields);
	}

	/**
	 * 根据AppId删除安装日志
	 *
	 * @param string $app_id
	 * @return true|PwError
	 */
	public function delByAppId($app_id) {
		if (!$app_id) return new PwError('APPCENTER:validate.fail.appid.not.exit');
		$this->_load()->delByAppId($app_id);
		return true;
	}

	/**
	 * 根据app_id查找安装日志信息
	 *
	 * @param string $app_id
	 * @return PwError|Ambigous <Ambigous, multitype:, multitype:multitype: Ambigous <multitype:, multitype:unknown , mixed> >
	 */
	public function findByAppId($app_id) {
		if (!$app_id) return new PwError('APPCENTER:validate.fail.appid.not.exit');
		$_r = $this->_load()->fetchByAppId($app_id);
		foreach ($_r as $key => $value) {
			$_r[$key]['data'] = unserialize($value['data']);
		}
		return $_r;
	}

	/**
	 * @return PwApplicationLogDao
	 */
	private function _load() {
		return Wekit::loadDao('APPCENTER:service.dao.PwApplicationLogDao');
	}
}

?>