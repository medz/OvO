<?php
/**
 * 
 * 站内应用框架
 *
 * @author Mingqu Luo<luo.mingqu@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind
 */
class AppsController extends PwBaseController {
	
	public $appid = 0;
	
	public function beforeAction($handlerAdapter) {
		$this->appid = $this->getInput('appid');
		parent::beforeAction($handlerAdapter);
		if (!$this->loginUser->isExists()) {
			$this->forwardAction('u/login/run', array('backurl' => WindUrlHelper::createUrl('appcenter/app/run', array('appid' => $this->appid))));
		}
	}
	
	public function run() {
		$params = array ();
		$params['uid'] =  $this->loginUser->uid;

		list($status, $result) = $this->apiRequest('platform.request.geturl', $params);
		if (!$status) $this->showError(array('APPCENTER:get.app.url.fail', array('{{error}}' => $result)));
		
		$appUrl = $result;
		$this->setOutput($appUrl, 'appUrl');
	}
	
	/**
	 * 从云平台上获取
	 *
	 */
	public function apiRequest($method, $params = array()) {
		$params['method'] = $method;
		$params['url'] = 'http://'.$_SERVER ['HTTP_HOST'];
		//$params['url'] = 'http://www.wekit.net';
		$params['app_id'] =  $this->appid;
		$params ['timestamp'] = Pw::getTime();
		$params['sign'] = $this->createSign($params);
	
		require_once Wind::getRealPath ( "ACLOUD:system.core.ACloudSysCoreHttpclient" );
		$result = ACloudSysCoreHttpclient::post ($this->_getCloudApi(), $this->createHttpQuery($params));
		$result = WindJson::decode($result);
		
		if (!is_array($result) || !isset($result['code'])) return array(false, '');
		if ($result['code'] != 0) return array(false, $result['msg'] . ' '. $result['code']);
		return array(true, $result['result']);
		
	}

	private function _getCloudApi() {
		$filePath = Wind::getRealPath('APPCENTER:conf.cloudplatformurl.php', true);
		$openPlatformUrl = Wind::getComponent('configParser')->parse($filePath);
		return sprintf ( "%sapi.php?", $openPlatformUrl);
	}

	public function createSign($params) {
		if (empty($params) || !is_array($params)) return '';

		$keysService = ACloudSysCoreCommon::loadSystemClass ( 'keys', 'config.service' );
		$key1 = $keysService->getKey1 ( 1 );
		if (! $key1 || strlen ( $key1 ) != 128)
			return '';

		ksort ( $params );

		return md5( $this->createHttpQuery ( $params ) . $key1 );
	}

	public function createHttpQuery($params) {
		if (empty($params) || ! is_array ( $params )) {
			return '';
		}

		if (function_exists ( "http_build_query" ))
			return http_build_query ( $params );
		
		if (empty($params) || ! is_array ( $params )) {
			return '';
		}

		$query = '';
		foreach ( $params as $key => $value ) {
			$query .= "$key=" . urlencode ( $value ) . '&';
		}

		return $query;
	}

}