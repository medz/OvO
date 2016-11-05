<?php
/**
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright ©2003-2010 phpwind.com
 * @license
 */

class PwUserTagDm extends PwBaseDm {
	public $tag_id = 0;
	
	/**
	 * 设置标签ID
	 *
	 * @param int $tag_id
	 * @return PwUserTagDm
	 */
	public function setTagid($tag_id) {
		$this->tag_id = intval($tag_id);
		return $this;
	}
	
	/**
	 * 设置标签名字
	 *
	 * @param string $name
	 * @return PwUserTagDm
	 */
	public function setName($name) {
		$this->_data['name'] = trim($name);
		return $this;
	}
	
	/**
	 * 设置该标签是否为热门标签
	 *
	 * @param int $ifhot
	 * @return PwUserTagDm
	 */
	public function setIfhot($ifhot) {
		$this->_data['ifhot'] = intval($ifhot);
		return $this;
	}
	
	/**
	 * 该标签的使用次数
	 *
	 * @param int $count
	 * @return PwUserTagDm
	 */
	public function setUsedcount($count) {
		$this->_data['used_count'] = intval($count);
		return $this;
	}
	
	/**
	 * 设置增加值
	 *
	 * @param int $count
	 * @return PwUserTagDm
	 */
	public function increaseCount($count) {
		$this->_increaseData['used_count'] = intval($count);
		return $this;
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeAdd()
	 */
	protected function _beforeAdd() {
		if (!isset($this->_data['name'])) new PwError('USER:tag.name.require');
		if (true !== ($r= $this->_checkName($this->_data['name']))) return $r;
		if (!$this->getField('ifhot')) {
			$this->_data['ifhot'] = 1;
		}
		return true;
	}

	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeUpdate()
	 */
	protected function _beforeUpdate() {
		if ($this->tag_id < 1) return new PwError('USER:tag.id.require');
		if ($this->_data['name']) {
			if (true !== ($r= $this->_checkName($this->_data['name']))) return $r;
		}
		return true;
	}
	
	/**
	 * 检查数据合法性
	 * 
	 * @return boolean|PwError
	 */
	private function _checkName($name) {
		if (!$name) return new PwError('USER:tag.name.require');
		$len = Pw::strlen($name);
		//标签名称的长度为1 - 8个字
		if ($len > 8 || $len < 1) {
			return new PwError('USER:tag.name.length.error');
		}
		//标签名称只支持中文、字母、数字、下划线和小数点哦
		if (!preg_match('/^[\x7f-\xff\dA-Za-z\.\_]+$/', $name)) {
			return new PwError('USER:tag.name.format.error');
		}
		return true;
	}
}