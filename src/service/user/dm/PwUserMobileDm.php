<?php

/**
 * 用户手机DM
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwUserMobileDm extends PwBaseDm {
	
	public $id;

	public function __construct($id=0) {
		$id = intval($id);
		$id > 0 && $this->id = $id;
	}
	
	/**
	 * 设置用户UID
	 *
	 * @param int $uid
	 * @return PwUserMobileDm
	 */
	public function setUid($uid){
		$this->_data['uid'] = intval($uid);
		return $this;
	}
	
	/**
	 * 设置手机
	 *
	 * @param int $mobile
	 * @return PwUserMobileDm
	 */
	public function setMobile($mobile){
		$this->_data['mobile'] = $mobile;
		return $this;
	}
	
	/**
	 * 设置num
	 *
	 * @param int $num
	 * @return PwUserMobileDm
	 */
	public function setNumber($num){
		$this->_data['number'] = $num;
		return $this;
	}
	
	/**
	 * 设置时间
	 *
	 * @param int $create_time
	 * @return PwUserMobileDm
	 */
	public function setCreteTime($create_time){
		$this->_data['create_time'] = intval($create_time);
		return $this;
	}
	
	/**
	 * 设置验证码
	 *
	 * @param int $code
	 * @return PwUserMobileDm
	 */
	public function setCode($code){
		$this->_data['code'] = $code;
		return $this;
	}
	
	protected function _beforeAdd() {
		$this->_data['create_time'] = Pw::getTime();
		$this->_data['expired_time'] = $this->_data['create_time'] + 3600;
		return true;
	}
	
	protected function _beforeUpdate() {
		return true;
	}
}
?>