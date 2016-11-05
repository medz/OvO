<?php

/**
 * 用户注册引导service
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserRegisterGuideService.php 20274 2012-10-25 07:49:56Z yishuo $
 * @package srv.products.u.service
 */
class PwUserRegisterGuideService {
	private $minKey = '';
	
	/**
	 * 是否有引导
	 *
	 * @return boolean
	 */
	public function hasGuide() {
		return $this->_getOpenGuide() ? true : false;
	}
	
	/**
	 * 根据类型获得下一个用户引导数据
	 *
	 * @param string $key
	 * @return array|null
	 */
	public function getNextGuide($key) {
		$data = $this->_getData();
		$list = $this->orderList($data);
		if (!$key || !isset($list[$key])) {
			$next = $this->minKey;
		} else {
			$next = $list[$key]['next'];
		}
		return $list[$next] ? $list[$next]['value'] : null;
	}
	
	/**
	 * 获得所有引导
	 * 
	 * @return array
	 */
	public function getGuideList() {
		$guideList = $this->_getData();
		$config = $this->getConfig();
		$orderList = array();
		foreach ($guideList as $key => $item) {
			$item['order'] = $config[$key]['order'];
			$item['isopen'] = isset($config[$key]['isopen']) ? $config[$key]['isopen'] : 0;
			if ($orderList) {
				$_tmpResult = array();
				foreach ($orderList as $_key => $_item) {
					if ($config[$_key]['order'] > $config[$key]['order']) {
						$_tmpResult[$key] = $item;
						$_tmpResult[$_key] = $_item;
					} else {
						$_tmpResult[$_key] = $_item;
						$_tmpResult[$key] = $item;
					}
				}
				$orderList = $_tmpResult;
			} else {
				$orderList[$key] = $item;
			}
		}
		return $orderList;
	}
	
	/**
	 * 设置配置
	 *
	 * @param array $config
	 * @return bolean
	 */
	public function setConfig($config) {
		$configBo = new PwConfigSet('register');
		return $configBo->set('guide', $config)->flush();
	}

	/**
	 * 获得guide配置
	 * 
	 * @return array
	 */
	public function getConfig() {
		$config = Wekit::C('register', 'guide');
		return $config ? $config : array();
	}
		
	/**
	 * 排序数组
	 * 
	 * 返回排序好的队列，队列中的每个项包含：
	 * 1、pre: 该元素的前一个项的key
	 * 2、next: 该项的后一个项的key
	 * 3、value: 该项的具体值
	 * 4、order
	 *
	 * @param array $data
	 * @return array
	 */
	private function orderList($data) {
		static $list = array();
		if ($list) return $list;
		$openGuids = $this->_getOpenGuide();
		if (!$openGuids) return array();
		$_tempOpen = array_keys($openGuids);
		$min = null;
		foreach ((array)$data as $key => $value) {
			if (!in_array($key, $_tempOpen)) continue;
			$_tmp = array('pre' => null, 'value' => $value, 'next' => null, 'order' => $openGuids[$key]);
			if (null === $min || $min > $openGuids[$key]) {
				$min = $openGuids[$key];
				$this->minKey = $key;
			}
			$insert = false;
			foreach ($list as $_key => $_item) {
				if ($_item['order'] > $_tmp['order']) {
					$_pre = $list[$_key]['pre'];
					$list[$_key]['pre'] = $key;
					$_tmp['next'] = $_key;
					if (isset($list[$_pre])) {
						$list[$_pre]['next'] = $key;
						$_tmp['pre'] = $_pre;
					}
					$insert = true;
					break;
				} elseif ($_item['order'] == $_tmp['order']) {
					$_tmp['pre'] = $_key;
					$_tmp['next'] = $list[$_key]['next'];
					$list[$_key]['next'] = $key;
					if (isset($list[$_tmp['next']])) {
						$list[$_tmp['next']]['pre'] = $key;
					}
					$insert = true;
					break;
				}
			}
			if (false === $insert && count($list) >= 1) {
				$_tmp['pre'] = $_key;
				$list[$_key]['next'] = $key;
			}
			$list[$key] = $_tmp;
		}
		return $list;
	}
	
	/**
	 * 获得开启的注册引导向
	 * 
	 * @return array
	 */
	private function _getOpenGuide() {
		$config = $this->getConfig();
		if (!$config) return array();
		$_open = array();
		foreach ($config as $key => $_c) {
			if (1 == $_c['isopen']) {
				$_open[$key] = $_c['order'];
			}
		}
		return $_open;
	}
	
	/**
	 * 获取数据
	 * //TODO 数据库操作
	 * @return array
	 */
	private function _getData() {
		$menuFile = Wind::getRealPath('APPS:u.conf.registerguide.php', true);
		/* @var $_configParser WindConfigParser */
		$_configParser = Wind::getComponent('configParser');
		return $_configParser->parse($menuFile);
	}
}