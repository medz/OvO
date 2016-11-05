<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.threadList.PwThreadDataSource');

/**
 * 空间-帖子列表页-访问他人空间的列表
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwSpaceThread.php 19290 2012-10-12 08:13:34Z xiaoxia.xuxx $
 * @package wind
 */
class PwSpaceThread extends PwThreadDataSource {
	private $spaceid = 0;
	
	/**
	 * 构造函数
	 *
	 * @param int $spaceid
	 * @param int $loginUid
	 */
	public function __construct($spaceid) {
		$this->spaceid = $spaceid;
	}
	
	/* (non-PHPdoc)
	 * @see PwThreadDataSource::getTotal()
	 */
	public function getTotal() {
		return $this->_getThreadDs()->countThreadByUid($this->spaceid);
	}
	
	/* (non-PHPdoc)
	 * @see PwThreadDataSource::getData()
	 */
	public function getData($limit, $offset) {
		return $this->_getThreadDs()->getThreadByUid($this->spaceid, $limit, $offset);
	}
	
	/**
	 * 帖子的DS服务
	 *
	 * @return PwThread
	 */
	protected function _getThreadDs() {
		return Wekit::load('forum.PwThread');
	}
}