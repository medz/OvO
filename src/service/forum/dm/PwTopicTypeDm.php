<?php
/**
 * 主题分类模型
 * 
 * @author peihong <jhqblxt@gmail.com> Nov 23, 2011
 * @link
 * @version $Id: PwTopicTypeDm.php 15230 2012-08-02 05:55:42Z peihong.zhangph $
 * @license
 */
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');


class PwTopicTypeDm extends PwBaseDm {
	
	private $id;
	
	public function __construct($id = 0){
		$id = intval($id);
		if ($id < 1) return;
		$this->id = $id;		
	}
	
	public function setFid($fid) {
		$this->_data['fid'] = intval($fid);
		return $this;
	}

	public function setParentId($id) {
		$this->_data['parentid'] = intval($id);
		return $this;
	}
	
	public function setName($name) {
		$this->_data['name'] = trim($name);
		return $this;
	}

	public function setVieworder($vieworder) {
		$this->_data['vieworder'] = intval($vieworder);
		return $this;
	}

	public function setLogo($logo) {
		$this->_data['logo'] = $logo;
		return $this;
	}
	
	public function setIsSystem($isSystem) {
		$this->_data['issys'] = intval($isSystem);
		return $this;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getParentId(){
		return $this->_data['parentid'];
	}
	
	public function checkName(){
		if ( !$this->_data['name'] ) {
			return new PwError('BBS:forum.TOPIC_TYPE_NAME_EMPTY');
		} elseif (strlen( $this->_data['name']) > 120 ) {
			return new PwError('BBS:forum.TOPIC_TYPE_NAME_LENGTH_LIMIT');
		}
		return true;
	}
	
	public function checkFid(){
		if ( $this->_data['fid'] < 1) {
			return new PwError('BBS:forum.TOPIC_TYPE_FID_ERROR');
		}
		return true;
	}
	
	public function checkData() {
		if (empty($this->_data)) {
			return new PwError('BBS:forum.TOPIC_TYPE_DATA_IS_EMPTY');
		}
		return true;
	}
	
	public function _beforeAdd() {
		($result = $this->checkData()) === true
			&& ($result = $this->checkName()) === true
			&& ($result = $this->checkFid()) === true;
		return $result;
	}

	public function _beforeUpdate() {
		($result = $this->checkData()) === true
			&& ($result = $this->checkName() === true)
			&& ($result = $this->checkFid() === true);
		return $result;
	}
}