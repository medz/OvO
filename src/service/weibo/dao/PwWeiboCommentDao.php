<?php

/**
 * 新鲜事dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwWeiboCommentDao.php 8959 2012-04-28 09:06:05Z jieyin $
 * @package forum
 */

class PwWeiboCommentDao extends PwBaseDao {
	
	protected $_table = 'weibo_comment';
	protected $_pk = 'comment_id';
	protected $_dataStruct = array('comment_id', 'weibo_id', 'content', 'extra', 'created_userid', 'created_username', 'created_time');
	
	/*
	public function getFresh($id) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE id=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($id));
	}

	public function getFreshByIds($ids){
		$sql = $this->_bindSql('SELECT * FROM %s WHERE id IN %s ORDER BY id DESC', $this->getTable(), $this->sqlImplode($ids));
		$smt = $this->getConnection()->query($sql);
		return $smt->fetchAll();
	}

	public function getFreshByType($type, $srcId) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE type=? AND src_id IN %s', $this->getTable(), $this->sqlImplode($srcId));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($type), 'id');
	}

	public function getWeibos($weibo_ids) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE weibo_id IN %s', $this->getTable(), $this->sqlImplode($weibo_ids));
		$smt = $this->getConnection()->query($sql);
		return $smt->fetchAll('weibo_id');
	}*/

	public function getComment($weibo_id, $limit, $offset, $asc) {
		$orderby = $asc ? 'ASC' : 'DESC';
		$sql = $this->_bindSql('SELECT * FROM %s WHERE weibo_id=? ORDER BY created_time %s %s', $this->getTable(), $orderby , $this->sqlLimit($limit, $offset));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($weibo_id), 'comment_id');
	}

	public function addComment($fields) {
		return $this->_add($fields);
	}

	public function batchDeleteCommentByWeiboId($weiboIds) {
		$sql = $this->_bindSql('DELETE FROM %s WHERE weibo_id IN %s', $this->getTable(), $this->sqlImplode($weiboIds));
		$this->getConnection()->execute($sql);
		return true;
	}

	/*
	public function batchDelete($ids) {
		$sql = $this->_bindSql('DELETE FROM %s WHERE id IN %s', $this->getTable(), $this->sqlImplode($ids));
		$this->getConnection()->execute($sql);
		return true;
	}
	
	public function updateForum($fid, $fields, $increaseFields = array()) {
		if (!$fields = $this->_filterStruct($fields)) {
			return false;
		}
		$sql = $this->_bindTable('UPDATE %s SET ') . $this->sqlSingle($fields) . ' WHERE fid=?';
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($fid));
	}*/
}