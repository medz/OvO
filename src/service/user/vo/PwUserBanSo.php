<?php

/**
 * 用户禁止信息的搜索的Data-object
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserBanSo.php 21659 2012-12-12 07:00:13Z xiaoxia.xuxx $
 * @package src.service.user.do
 */
class PwUserBanSo {
	private $data = array();
	private $argsUrl = array();
	private $type = '';
	private $keywords = '';

	/**
	 * 设置搜索的类型'username'/'uid'
	 * 
	 * @param string $type
	 * @return PwUserBanSo
	 */
	public function setType($type) {
		$this->type = $type;
		$this->argsUrl['key'] = $type;
		return $this;
	}

	/**
	 * 设置搜索类型对应的值
	 * 
	 * @param string $keywords
	 * @return PwUserBanSo
	 */
	public function setKeywords($keywords) {
		$this->keywords = $keywords;
		$this->argsUrl['value'] = $keywords;
		return $this;
	}

	/**
	 * 设置 创建禁止的人
	 * 
	 * @param string $created_username
	 * @return PwUserBanSo
	 */
	public function setCreatedUsername($created_username) {
		$this->argsUrl['operator'] = $created_username;
		if ('system' != strtolower($created_username)) {
			/* @var $userDs PwUser */
			$userDs = Wekit::load('user.PwUser');
			$info = $userDs->getUserByName($created_username);
			if ($info) {
				$this->data['created_userid'] = $info['uid'];
			}
		} else {
			$this->data['created_userid'] = 0;
		}
		return $this;
	}
	
	/**
	 * 设置禁止开始的时间
	 * 
	 * @param string $start_time
	 * @return PwUserBanSo
	 */
	public function setStartTime($start_time) {
		$this->argsUrl['start_time'] = $start_time;
		$this->data['start_time'] = $start_time ? Pw::str2time($start_time) : '';
		return $this;
	}

	/**
	 * 设置禁止结束的时间
	 * 
	 * @param string $end_time
	 * @return PwUserBanSo
	 */
	public function setEndTime($end_time) {
		$this->argsUrl['end_time'] = $end_time;
		$this->data['end_time'] = $end_time ? Pw::str2time($end_time) : '';
		return $this;
	}
	
	/**
	 * 以数组格式获得数据
	 *
	 * @return array
	 */
	public function getData() {
		if ($this->keywords) {
			if ($this->type == 'username') {
				/* @var $userDs PwUser */
				$userDs = Wekit::load('user.PwUser');
				$info = $userDs->getUserByName($this->keywords);
				if ($info) {
					$this->data['uid'] = $info['uid'];
				}
			} else {
				$this->data['uid'] = $this->keywords;
			}
		}
		return $this->data;
	}
	
	/**
	 * 获得搜索条件
	 *
	 * @param string $field
	 * @return string
	 */
	public function getCondition($field) {
		return isset($this->argsUrl[$field]) ? $this->argsUrl[$field] : '';
	}
	
	/**
	 * 获得分页数据
	 * 
	 * @return array
	 */
	public function getArgsUrl() {
		return $this->argsUrl;
		/* $url = array();
		foreach ($this->argsUrl as $key => $val) {
			$val && $url[] = $key . '=' . $val;
		}
		return implode('&', $url); */
	}
}