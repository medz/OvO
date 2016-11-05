<?php
/**
 * 用户组权限数据模型
 * 
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Nov 1, 2011
 * @link http://www.phpwind.com
 * @copyright 2011 phpwind.com
 * @license
 * @version $Id: PwUserPermissionDm.php 21226 2012-12-03 03:57:42Z jieyin $
 */

Wind::import('LIB:base.PwBaseDm');

class PwUserPermissionDm extends PwBaseDm {
	
	private $gid = 0;
	private $permission = array();
	
	public function __construct($gid) {
		$gid = intval($gid);
		$this->gid = $gid;
	}
	
	public function getGid(){
		return $this->gid;
	}

	public function getPermission() {
		return $this->permission;
	}

	/**
	 * 返回格式化数组,For Dao
	 */
	public function getData() {
		$data = array();
		if (!$this->gid || !$this->permission) return $data;

		$config = Wekit::load('usergroup.srv.PwPermissionService')->getPermissionConfig();
		foreach ($this->permission as $k => $v) {
			$vtype = 'string';
			if (is_array($v)) {
				$vtype = 'array';
				$v = serialize($v);
			}
			$data[] = array($this->gid, $k, $config[$k][1], $v, $vtype);
		}
		return $data;
	}

	public function setPermission($key, $value) {
		$method = 'set' . ucfirst($key);
		if (method_exists($this, $method)) {//自定义型
			return $this->{$method}($value);
		}
		$this->permission[$key] = $value;
	}

	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeAdd()
	 */
	protected function _beforeAdd() {
		return true;
	}

	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeUpdate()
	 */
	protected function _beforeUpdate() {
		return true;
	}
}