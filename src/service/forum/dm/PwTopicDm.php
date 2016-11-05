<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.dm.PwPostDm');
Wind::import('SRV:forum.PwThread');

/**
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwTopicDm.php 24888 2013-02-25 08:12:54Z jieyin $
 * @package forum
 */

class PwTopicDm extends PwPostDm {
	
	public $tid;

	public function __construct($tid=0, PwForumBo $forum = null, PwUserBo $user = null) {
		parent::__construct($forum, $user);
		$this->tid = $tid;
	}

	public function setLastpost($uid, $username, $time=null) {
		$this->_data['lastpost_userid'] = $uid;
		$this->_data['lastpost_username'] = $username;
		is_numeric($time) && $this->_data['lastpost_time'] = $time;
		return $this;
	}

	public function setLastposttime($time) {
		$this->_data['lastpost_time'] = intval($time);
		return $this;
	}

	public function addLastposttime($time) {
		$this->_increaseData['lastpost_time'] = intval($time);
		return $this;
	}
	
	public function setSpecial($special) {
		$this->_data['special'] = $special;
		return $this;
	}

	public function setDigest($digest) {
		$this->_data['digest'] = intval($digest);
		return $this;
	}

	public function setTopped($topped) {
		$this->_data['topped'] = intval($topped);
		return $this;
	}

	public function setSpecialsort($specialsort) {
		$this->_data['special_sort'] = intval($specialsort);
		return $this;
	}
	
	public function setTopictype($type) {
		$this->_data['topic_type'] = intval($type);
		return $this;
	}
	
	public function setTpcstatus($tpcstatus) {
		$this->_data['tpcstatus'] = intval($tpcstatus);
		return $this;
	}
	
	public function setLocked($bool) {
		$this->_bitData['tpcstatus'][PwThread::STATUS_LOCKED] = (bool)$bool;
		return $this;
	}

	public function setClosed($bool) {
		$this->_bitData['tpcstatus'][PwThread::STATUS_CLOSED] = (bool)$bool;
		return $this;
	}

	public function setDowned($bool) {
		$this->_bitData['tpcstatus'][PwThread::STATUS_DOWNED] = (bool)$bool;
		return $this;
	}
	
	public function setOperatorLog($bool) {
		$this->_bitData['tpcstatus'][PwThread::STATUS_OPERATORLOG] = (bool)$bool;
		return $this;
	}
	
	public function setHighlight($highlight) {
		$this->_data['highlight'] = $highlight;
		return $this;
	}
	
	public function setOvertime($overtime) {
		$this->_data['overtime'] = intval($overtime);
		return $this;
	}
	
	public function setInspect($inspect) {
		$this->_data['inspect'] = $inspect;
		return $this;
	}
	
	public function setIfshield($ifshield) {
		$this->_data['ifshield'] = intval($ifshield);
		return $this;
	}
	
	public function addHits($num) {
		$this->_increaseData['hits'] = intval($num);
		return $this;
	}
	
	public function addReplyTopped($num) {
		$this->_increaseData['reply_topped'] = intval($num);
		return $this;
	}
	
	public function checkTitle() {
		if ($this->_data['subject'] === '') {
			return new PwError('BBS:post.subject.empty');
		}
		return parent::checkTitle();
	}

	protected function _getThreadsService() {
		return Pw::load('forum.PwThread');
	}
}
?>