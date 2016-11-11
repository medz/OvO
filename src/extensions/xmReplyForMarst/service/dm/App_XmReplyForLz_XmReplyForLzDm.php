<?php
defined('WEKIT_VERSION') or exit(403);
/**
 * App_XmReplyForLz_XmReplyForLzDm - 数据模型
 *
 * @author 蝦米 <>
 * @copyright 
 * @license 
 */
class App_XmReplyForLz_XmReplyForLzDm extends PwBaseDm {
	protected $id;
	
	/**
	 * @return field_type
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param field_type $id
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	/**
	 * set table field value
	 *
	 * @param mixed $field_value
	 */
	public function setField($field_value) {
		$this->_data['field'] = $field_value;
	}

	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeAdd()
	 */
	protected function _beforeAdd() {
		// TODO Auto-generated method stub
		//check the fields value before add
		return true;
	}

	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeUpdate()
	 */
	 protected function _beforeUpdate() {
		// TODO Auto-generated method stub
	 	//check the fields value before update
		return true;
	}
}

?>