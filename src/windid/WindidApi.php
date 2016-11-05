<?php
//!defined('WINDID') && define('WINDID', dirname(__FILE__));
define('WINDID_BOOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
!defined('WINDID_VERSION') && define('WINDID_VERSION', '1.0.0');

require WINDID_BOOT . 'bootstrap.php';

class WindidApi {

	public static function api($api) {
		static $cls = array();
		$array = array('user', 'config', 'message', 'avatar', 'area', 'school', 'app', 'notify');
		if (!in_array($api, $array)) return WindidError::FAIL;
		$class = 'Windid' . ucfirst($api) . 'Api';
		if (!isset($cls[$class])) {
			if (WINDID_CONNECT == 'db') {
				$class = Wind::import('WINDID:api.local.' . $class);
				$cls[$class] = new $class();
			} elseif (WINDID_CONNECT == 'http') {
				$class = Wind::import('WINDID:api.web.' . $class);
				$cls[$class] = new $class();
			} else {
				return WindidError::FAIL;
			}
		}
		return $cls[$class];
	}

	public static function open($script, $getData = array(), $postData = array(), $method='post', $protocol='http') {
		$time = Pw::getTime();
		list($c, $a) = explode('/', $script);

		$getData['m'] = 'api';
		$getData['c'] = $c;
		$getData['a'] = $a;
		$getData['clientid'] = WINDID_CLIENT_ID;
		$getData['time'] = $time;
		$getData['windidkey'] = WindidUtility::appKey(WINDID_CLIENT_ID, $time, WINDID_CLIENT_KEY, $getData, $postData);
		$url = WINDID_SERVER_URL  . '/index.php?' . http_build_query($getData);

        if(!(strpos($url, 'http://')===0 || strpos($url, 'https://')===0)) {
            return false;
        }
        //$result = file_get_contents($url);
		$result = WindidUtility::buildRequest($url, $postData);
		if ($result === false) return WindidError::SERVER_ERROR;
		return Pw::jsonDecode($result);
	}

	public static function getDm($api) {
		$array = array('user', 'message', 'credit', 'app');
		if (!in_array($api, $array)) return WindidError::FAIL;
		switch ($api) {
			case 'user':
				return Wind::import('WSRV:user.dm.WindidUserDm');
			case 'message':
				return Wind::import('WSRV:message.dm.WindidMessageDm');
			case 'credit':
				return Wind::import('WSRV:user.dm.WindidCreditDm');
			case 'app':
				return Wind::import('WSRV:app.dm.WindidAppDm');
		}
	}

	public static function app() {
		return Wekit::app('windid');
	}

	public static function C($namespace = '', $key = '') {
		return Wekit::app('windid')->config->C($namespace, $key);
	}
}