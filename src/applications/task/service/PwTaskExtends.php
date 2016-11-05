<?php

/**
 * 任务扩展服务层
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskExtends.php 7054 2012-03-29 04:04:21Z xiaoxia.xuxx $
 * @package src.modules.task.service
 */
class PwTaskExtends {
	private $config = null;
	
	/**
	 * 获得奖励扩展列表
	 * 
	 * @param array $reward 设置的奖励值
	 * @return array
	 */
	public function getRewardTypeList($reward = array()) {
		$list = $this->getExtendsList('reward');
		unset($reward['type']);
		$key = $reward ? serialize((array)$reward) : '';
		return $this->buildList($list, $key);
	}
	
	/**
	 * 获得完成条件扩展列表
	 * 
	 * @param array $condition 设置的条件值
	 * @return array
	 */
	public function getConditionTypeList($condition = array()) {
		$list = $this->getExtendsList('condition');
		unset($condition['type'], $condition['child']);
		$var = $condition ? serialize((array)$condition) : '';
		$return = array();
		foreach ($list as $key => $item) {
			$return[$key] = array('title' => $item['title']);
			$return[$key]['children'] = $this->buildList($item['children'], $var);
		}
		return $return;
	}
	
	/**
	 * 构建输出数据格式
	 *
	 * @param array $data
	 * @param string $var  传递给用户的参数
	 * @return array
	 */
	private function buildList($data, $var = '') {
		$return = array();
		foreach ($data as $key => $item) {
			$return[$key] = array('title' => $item['title'], 'var' => $var, 'url' => $item['setting_url'] ? WindUrlHelper::createUrl($item['setting_url']) : '');
		}
		return $return;
	}
	
	/**
	 * 获得扩展列表
	 * 
	 * @param string $type
	 * @return array
	 */
	private function getExtendsList($type) {
		if ($this->config === null) {
			$this->config = include (Wind::getRealPath('APPS:task.conf.taskExtends.php', true));
		}
		return isset($this->config[$type]) ? $this->config[$type] : array();
	}
}