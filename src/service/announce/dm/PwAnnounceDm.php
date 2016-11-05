<?php
defined('WEKIT_VERSION') || exit('Forbidden');
Wind::import('LIB:base.PwBaseDm');

/**
 * 公告管理基础表数据模型
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwAnnounceDm.php 2781 下午01:42:16Z mingxing.sun $
 * @package wind
 */
class PwAnnounceDm extends PwBaseDm {
	public $aid = 0;
	public $_data = array();

	public function __construct($aid = 0) {
		$this->aid = $aid;
	}

	/**
	 * 设置顺序
	 *
	 * @param int 公告显示顺序
	 * @return object
	 */
	public function setVieworder($vieworder) {
		$this->_data['vieworder'] = (int) $vieworder;
		return $this;
	}

	/**
	 * 设置用户ID
	 * 
	 * @param int $uid
	 * @return object
	 */
	public function setUid($uid) {
		$this->_data['created_userid'] = (int) $uid;
		return $this;
	}

	/**
	 * 设置公告类别ID 0为文字公告  1为链接公告
	 *
	 * @param int $typeid
	 * @return object
	 */
	public function setTypeid($typeid) {
		$this->_data['typeid'] = (int) $typeid;
		return $this;
	}

	/**
	 * 设置公告链接地址
	 *
	 * @param string $url
	 * @return object
	 */
	public function setUrl($url) {
		$this->_data['url'] = $url;
		return $this;
	}

	/**
	 * 设置公告标题
	 *
	 * @param string $subject
	 * @return object
	 */
	public function setSubject($subject) {
		$this->_data['subject'] = $subject;
		return $this;
	}

	/**
	 * 设置公告内容
	 *
	 * @param string $content
	 * @return object
	 */
	public function setContent($content) {
		$this->_data['content'] = $content;
		return $this;
	}

	/**
	 * 设置公告发布时间
	 *
	 * @param int $startDate
	 * @return object
	 */
	public function setStartDate($startDate) {
		$time = $startDate ? Pw::str2time($startDate) : Pw::str2time(Pw::time2str(Pw::getTime(), 'Y-m-d'));
		$this->_data['start_date'] = (int)$time;
		return $this;
	}

	/**
	 * 设置公告结束时间
	 *
	 * @param int $endDate
	 * @return object
	 */
	public function setEndDate($endDate) {
		$time = $endDate ? Pw::str2time($endDate) : 9999999999;
		$this->_data['end_date'] = (int)$time;
		return $this;
	}

	/**
	 * 预处理机制
	 * 
	 * @return boolean
	 */
	public function _beforeUpdate() {
		if ($this->_data['start_date'] && $this->_data['end_date'] && ($this->_data['end_date'] < $this->_data['start_date'])) {
			return new PwError('ANNOUNCE:date.error');
		}
		if (isset($this->_data['subject']) && !$this->_data['subject']) {
			return new PwError('ANNOUNCE:subject.require');
		}
		if (isset($this->_data['typeid'])) {
			if ($this->_data['typeid'] == 0 && isset($this->_data['content']) && !$this->_data['content']) {
				return new PwError('ANNOUNCE:content.require');
			}
			if ($this->_data['typeid'] == 1 && isset($this->_data['url']) && !$this->_data['url']) {
				return new PwError('ANNOUNCE:url.require');
			}
		} else {
			unset($this->_data['content'], $this->_data['url']);
		}
		return true;
	}

	/**
	  * 添加前预处理
	  *
	  * @return boolean
	  */
	public function _beforeAdd() {
		if ($this->_data['start_date'] && $this->_data['end_date'] && ($this->_data['end_date'] < $this->_data['start_date'])) {
			return new PwError('ANNOUNCE:date.error');
		}
		if (!isset($this->_data['subject']) || !$this->_data['subject']) {
			return new PwError('ANNOUNCE:subject.require');
		}
		if (!isset($this->_data['typeid'])) {
			return new PwError('ANNOUNCE:typeid,require');
		}
		if ($this->_data['typeid'] == 0 && !$this->getField('content')) {
			return new PwError('ANNOUNCE:content.require');
		}
		if ($this->_data['typeid'] == 1 && !$this->getField('url')) {
			return new PwError('ANNOUNCE:url.require');
		}
		return true;
	}
}
?>