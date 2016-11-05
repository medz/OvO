<?php

Wind::import('SRV:forum.srv.manage.PwThreadManageDo');
Wind::import('SRV:forum.dm.PwTopicDm');

/**
 * 帖子管理操作-精华
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadManageDoDigest.php 24735 2013-02-19 03:23:38Z jieyin $
 * @package forum
 */

class PwThreadManageDoDigest extends PwThreadManageDo {
	
	public $digest;

	protected $tids;
	protected $isDeductCredit = true;
	protected $threads = array();
	
	/**
	 * 构造方法
	 *
	 * @param PwThreadManage $srv
	 */
	public function __construct(PwThreadManage $srv) {
		parent::__construct($srv);
	}
	
	/* (non-PHPdoc)
	 * @see PwThreadManageDo::check()
	 */
	public function check($permission) {
		return (isset($permission['digest']) && $permission['digest']) ? true : false;
	}
	
	/**
	 * 设置精华
	 *
	 * @param int $digest
	 * @return PwThreadManageDoDigest
	 */
	public function setDigest($digest) {
		$this->digest = intval($digest);
		return $this;
	}

	/* (non-PHPdoc)
	 * @see PwThreadManageDo::gleanData()
	 */
	public function gleanData($value) {
		if ($value['digest'] == $this->digest) return;
		$this->tids[] = $value['tid'];
		$this->threads[] = $value;
	}
	
	/* (non-PHPdoc)
	 * @see PwThreadManageDo::run()
	 */
	public function run() {
		if ($this->tids) {
			$topicDm = new PwTopicDm(true);
			$topicDm->setDigest($this->digest);
			Wekit::load('forum.PwThread')->batchUpdateThread($this->tids, $topicDm, PwThread::FETCH_MAIN);
			$this->_digest();
			$this->_operateUser();
			$this->_addManageLog();
		}
	}
	
	/**
	 * 精华处理
	 * 
	 * @return boolean
	 */
	private function _digest() {
		if (1 != $this->digest) {
			return Wekit::load('forum.PwThreadDigestIndex')->batchDeleteThread($this->tids);
		}
		Wind::import('SRV:forum.dm.PwThreadDigestDm');
		/* @var $srv PwForumService */
		$srv = Wekit::load('forum.srv.PwForumService');
		$digestDms = array();
		$fids = array();
		$time = Pw::getTime();
		foreach ($this->threads as $thread) {
			$_tmp = new PwThreadDigestDm($thread['tid']);
			if (!isset($fids[$thread['fid']])) {
				$fids[$thread['fid']] = $srv->getCateId($thread['fid']);
			}
			$_tmp->setCid($fids[$thread['fid']])
				->setFid($thread['fid'])
				->setCreatedTime($thread['created_time'])
				->setLastpostTime($thread['lastpost_time'])
				->setTopicType($thread['topic_type'])
				->setOperator($this->srv->user->username, $this->srv->user->uid, $time);
			$digestDms[] = $_tmp;
		}
		/* @var $digestDs PwThreadDigestIndex */
		$digestDs = Wekit::load('SRV:forum.PwThreadDigestIndex');
		return $digestDs->batchAddDigest($digestDms);
	}
	
	/**
	 * 用户相关操作
	 */
	private function _operateUser() {
		$operation = $this->digest == 1 ? 'digest_topic' : 'remove_digest';
		$digestNum = $this->digest == 1 ? 1 : -1;
		Wind::import('SRV:credit.bo.PwCreditBo');
		Wind::import('SRV:forum.bo.PwForumBo');
		/* @var $userDs PwUser */
		$userDs = Wekit::load('user.PwUser');
		$credit = PwCreditBo::getInstance();
		foreach ($this->threads as $thread) {
			//更新用户精华数
			$userInfo = new PwUserInfoDm($thread['created_userid']);
			$userInfo->addDigest($digestNum);
			$userDs->editUser($userInfo, PwUser::FETCH_DATA);
			//更新用户积分
			$forum = new PwForumBo($thread['fid']);
			$credit->operate($operation, PwUserBo::getInstance($thread['created_userid']), true, array('forumname' => $forum->foruminfo['name']), $forum->getCreditSet($operation));
			$credit->execute();
		}
		return true;
	}
	
	/**
	 * 添加日志的
	 */
	private function _addManageLog() {
		if ($this->digest == 1) {
			$type = 'degist';
		} else {
			$type = 'undegis';
		}
		/* @var $logSrv PwLogService */
		$logSrv = Wekit::load('log.srv.PwLogService');
		$logSrv->addThreadManageLog($this->srv->user, $type, $this->threads, $this->_reason);
		return true;
	}
}