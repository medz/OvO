<?php

/**
 * 附件dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwAttachDao.php 20516 2012-10-30 09:50:29Z jieyin $
 * @package attach
 */

class PwAttachDao extends PwBaseDao {
	
	protected $_table = 'attachs';
	protected $_pk = 'aid';
	protected $_dataStruct = array('aid', 'name', 'type', 'size', 'path', 'ifthumb', 'created_userid', 'created_time', 'app', 'descrip');
	
	public function getAttach($aid) {
		return $this->_get($aid);
	}

	public function fetchAttach($aids) {
		return $this->_fetch($aids);
	}

	public function addAttach($fields) {
		return $this->_add($fields);
	}

	public function updateAttach($aid, $fields) {
		return $this->_update($aid, $fields);
	}

	public function batchUpdateAttach($aids, $fields) {
		return $this->_batchUpdate($aids, $fields);
	}

	public function deleteAttach($aid) {
		return $this->_delete($aid);
	}

	public function batchDeleteAttach($aids) {
		return $this->_batchDelete($aids);
	}
}