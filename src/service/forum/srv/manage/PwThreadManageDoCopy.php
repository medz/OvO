<?php

Wind::import('SRV:forum.srv.manage.PwThreadManageDo');
Wind::import('SRV:forum.dm.PwTopicDm');
Wind::import('SRV:forum.dm.PwForumDm');

/**
 * 帖子管理操作-复制
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadManageDoDigest.php 14445 2012-07-20 09:16:44Z jinlong.panjl $
 * @package forum
 */

class PwThreadManageDoCopy extends PwThreadManageDo {
	
	public $fid;
	public $topictype;
	protected $tids;
	protected $isDeductCredit = true;
	protected $threads = array();
	
	public function __construct(PwThreadManage $srv){
		parent::__construct($srv);
	}
	
	public function check($permission) {
		if (!isset($permission['copy']) || !$permission['copy']) {
			return false;
		}
		if (isset($this->fid)) {
			Wind::import('SRV:forum.bo.PwForumBo');
			$forum = new PwForumBo($this->fid);
			if (!$forum->isForum()) {
				return new PwError('BBS:manage.error.copy.targetforum');
			}
			if ($this->topictype && !$forum->forumset['topic_type']) {

				return new PwError('BBS:post.topictype.closed');

			}

			if ($forum->forumset['topic_type'] && $forum->forumset['force_topic_type'] && !$this->topictype) {
				$topicTypes = Wekit::load('SRV:forum.PwTopicType')->getTypesByFid($forum->fid);
				if ($topicTypes) {
					return new PwError('BBS:post.topictype.empty');
				}
			}
		}
		return true;
	}

	public function gleanData($value) {
		$this->tids[] = $value['tid'];
		$this->threads[] = $value;
	}
	
	/**
	 * 设置需要复制到的版块
	 *
	 * @param int $fid
	 * @return int
	 */
	public function setFid($fid) {
		$this->fid = intval($fid);
		return $this;
	}
	
	/**
	 * 设置主题分类
	 *
	 * @param int $topictype
	 * @return int
	 */
	public function setTopictype($topictype) {
		$this->topictype = intval($topictype);
		return $this;
	}
	
	/**
	 * 复制帖子 | 复制特殊帖、附件等待做。。。
	 *
	 * @param int $topictype
	 * @return int
	 */
	public function run() {
		foreach ($this->threads as $v) {
			$topicDm = new PwTopicDm($v['tid']);
			$topicDm->setLastpost($v['lastpost_userid'],$v['lastpost_username'],$v['lastpost_time'])
					->setSpecial($v['special'])
					->setDigest($v['digest'])
					->setTopped($v['topped'])
					->setSpecialsort($v['special_sort'])
					->setTopictype($this->topictype)
					->setTpcstatus($v['tpcstatus'])
					->setHighlight($v['highlight'])
					->setOvertime($v['overtime'])
					->addHits($v['hits'])
					->setTitle($v['subject'])
					->setContent($v['content'])
					->setFid($this->fid)
					->setAuthor($v['created_userid'],$v['created_username'],$v['created_ip'])
					->setModifyInfo($v['modified_userid'],$v['modified_username'],$v['modified_ip'],$v['modified_time'])
					->setCreatedTime($v['created_time'])
					->setDisabled($v['disabled'])
					->setAids($v['aids'])
					->setIfupload($v['ifupload'])
					->setReplyNotice($v['reply_notice'])
					->setLikeCount($v['like_count'])
					->setSellCount($v['sell_count'])
					->addReplies($v['replies'])
					->addSellCount($v['sell_count'])
					->setReminds($v['reminds'])
					->setWordVersion($v['word_version'])
					->setTags($v['tags']);	
				
			$tid = $this->_getThreadDs()->addThread($topicDm);
			if ($tid) {
				PwSimpleHook::getInstance('PwThreadManageDoCopy')->runDo($topicDm, $tid);
				$forumDm = new PwForumDm($this->fid);
				$forumDm->addThreads(1);
				$forumDm->addArticle(1);
				Wekit::load('SRV:forum.PwForum')->updateForum($forumDm);
			}
		}

		//管理日志添加
		Wekit::load('log.srv.PwLogService')->addThreadManageLog($this->srv->user, 'copy', $this->threads, $this->_reason, $this->fid . '|' . $this->topictype);
	}
	
	/**
	 * Enter description here ...
	 *
	 * @return PwThread
	 */
	public function _getThreadDs() {
		return Wekit::load('forum.PwThread');
	}
}