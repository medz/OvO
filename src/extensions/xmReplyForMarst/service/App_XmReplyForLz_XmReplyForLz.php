<?php
defined('WEKIT_VERSION') or exit(403);
Wind::import('EXT:xmReplyForLz.service.dm.App_XmReplyForLz_XmReplyForLzDm');
/**
 * App_XmReplyForLz_XmReplyForLz - 数据服务接口
 *
 * @author 蝦米 <>
 * @copyright 
 * @license 
 */
class App_XmReplyForLz_XmReplyForLz {
	
	/**
	 * add record
	 *
	 * @param App_XmReplyForLz_XmReplyForLzDm $dm
	 * @return multitype:|Ambigous <boolean, number, string, rowCount>
	 */
	public function add(App_XmReplyForLz_XmReplyForLzDm $dm) {
		if (true !== ($r = $dm->beforeAdd())) return $r;
		return $this->_loadDao()->add($dm->getData());
	}
	
	/**
	 * update record
	 *
	 * @param App_XmReplyForLz_XmReplyForLzDm $dm
	 * @return multitype:|Ambigous <boolean, number, rowCount>
	 */
	public function update(App_XmReplyForLz_XmReplyForLzDm $dm) {
		if (true !== ($r = $dm->beforeUpdate())) return $r;
		return $this->_loadDao()->update($dm->getId(), $dm->getData());
	}
	
	/**
	 * get a record
	 *
	 * @param unknown_type $id
	 * @return Ambigous <multitype:, multitype:unknown , mixed>
	 */
	public function get($id) {
		return $this->_loadDao()->get($id);
	}
	
	/**
	 * delete a record
	 *
	 * @param unknown_type $id
	 * @return Ambigous <number, boolean, rowCount>
	 */
	public function delete($id) {
		return $this->_loadDao()->delete($id);
	}
	
	/**
	 * @return App_XmReplyForLz_XmReplyForLzDao
	 */
	private function _loadDao() {
		return Wekit::loadDao('EXT:xmReplyForLz.service.dao.App_XmReplyForLz_XmReplyForLzDao');
	}
}

?>