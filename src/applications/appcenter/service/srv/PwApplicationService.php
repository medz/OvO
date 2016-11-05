<?php
/**
 * 本地应用服务
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwApplicationService.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package products
 * @subpackage appcenter.service.srv
 */
class PwApplicationService {

	/**
	 * 添加应用信息
	 *
	 * @param PwApplicationDm $application
	 */
	public function add($application) {}

	/**
	 * 根据App_id删除应用信息，该操作会级联删除应用相关信息(log,hooks,injector)
	 *
	 * @param string $app_id
	 * @return true|PwError
	 */
	public function del($app_id) {
		if (!$app_id) return new PwError('validate.fail.appid.not.exit');
		$this->_loadAppDs()->delByAppId($app_id);
		$hooks = $this->_loadHookDs()->fetchByAppId($app_id);
		if ($hooks) {
			$_hookNames = array();
			foreach ($hooks as $hook) {
				$_hookNames[] = $hook['name'];
			}
			$injector = $this->_loadHookInjectDs()->batchDelByHookName($_hookNames);
		}
		$this->_loadHookDs()->delByAppId($app_id);
		return true;
	}

	/**
	 * @return PwHookInject
	 */
	private function _loadHookInjectDs() {
		return wekit::load('SRV:hook.PwHookInject');
	}

	/**
	 * @return PwHooks
	 */
	private function _loadHookDs() {
		return wekit::load('SRV:hook.PwHooks');
	}

	/**
	 * @return PwApplication
	 */
	private function _loadAppDs() {
		return Wekit::load('APPCENTER:service.PwApplication');
	}

	/**
	 * @return PwApplicationLog
	 */
	private function _loadAppLogDs() {
		return wekit::load('APPCENTER:service.PwApplicationLog');
	}
}

?>