<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.threadList.PwThreadDataSource');

/**
 * 获取我的帖子-我的主题列表
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwMyThread.php 19291 2012-10-12 08:14:16Z xiaoxia.xuxx $
 * @package wind
 */
class PwMyThread extends PwThreadDataSource {
	/**
	 * @var int
	 */
	private $uid = 0;
	
	/**
	 * 构造函数
	 *
	 * @param int $spaceid
	 * @param int $loginUid
	 */
	public function __construct($uid) {
		$this->uid = $uid;
	}
	
	/* (non-PHPdoc)
	 * @see PwThreadDataSource::getTotal()
	 */
	public function getTotal() {
		return $this->_getThreadExpandDs()->countDisabledThreadByUid($this->uid);
	}
	
	/* (non-PHPdoc)
	 * @see PwThreadDataSource::getData()
	 */
	public function getData($limit, $offset) {
		return $this->_getThreadExpandDs()->getDisabledThreadByUid($this->uid, $limit, $offset);
	}
	
	/**
	 *
	 * @return PwThreadExpand
	 */
	protected function _getThreadExpandDs() {
		return Wekit::load('forum.PwThreadExpand');
	}
}