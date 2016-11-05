<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:attention.PwFresh');
Wind::import('LIB:dataSource.PwDataLazyLoader');
Wind::import('LIB:dataSource.iPwDataSource2');
Wind::import('SRV:weibo.PwWeibo');
Wind::import('SRV:attention.srv.freshDisplay.PwFreshAttachDisplay');
Wind::import('LIB:ubb.PwSimpleUbbCode');
Wind::import('LIB:ubb.config.PwUbbCodeConvertThread');

/**
 * 新鲜事列表
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwFreshDisplay.php 22678 2012-12-26 09:22:23Z jieyin $
 * @package src.service.user.srv
 */

class PwFreshDisplay {
	
	protected $fresh = array();
	protected $fresh_src = array();
	protected $fresh_map = array(
		PwFresh::TYPE_THREAD_REPLY => 'PwReplyFresh',
		PwFresh::TYPE_THREAD_TOPIC => 'PwTopicFresh',
		PwFresh::TYPE_WEIBO => 'PwWeiboFresh'
	);

	public function __construct(iPwDataSource $ds) {
		$this->fresh = $ds->getData();
		$src_id = array();
		foreach ($this->fresh as $key => $value) {
			$src_id[$value['type']][] = $value['src_id'];
		}
		foreach ($src_id as $key => $value) {
			$this->fresh_src[$key] = new $this->fresh_map[$key]($value);
		}
		$this->_init();
	}
	
	/**
	 * 聚合内容
	 *
	 * @param array $relation 新鲜事关联
	 * @return array
	 */
	public function gather() {
		$fresh = array();
		foreach ($this->fresh as $key => $value) {
			$offer = $this->fresh_src[$value['type']]->offer($value['src_id']);
			$fresh[$key] = $value + $offer;
		}
		return $fresh;
	}

	protected function _init() {
		foreach ($this->fresh_map as $key => $value) {
			if ($this->fresh_src[$key]) {
				$this->fresh_src[$key]->init();
			}
		}
	}
}

abstract class PwBaseFresh {
	
	abstract public function init();

	abstract public function offer($id);

	protected function _bulidContent($array, &$errcode) {
		$errcode = array();
		$array['content'] = str_replace(array("\r", "\n", "\t"), '', $array['content']);
		$array['content'] = WindSecurity::escapeHTML($array['content']);
		if ($array['ifshield']) {
			$array['subject'] = '';
			$array['content'] = '<span style="text-decoration: line-through">此帖已被屏蔽</span>';
		} elseif ($array['useubb']) {
			$ubb = new PwUbbCodeConvertThread();
			$array['reminds'] && $ubb->setRemindUser($array['reminds']);
			$array['pic'] && $ubb->setAttachParser(new PwFreshAttachDisplay($array['pic']));
			$array['content'] = PwSimpleUbbCode::convert($array['content'], 140, $ubb);
			PwSimpleUbbCode::isSubstr() && $errcode['is_read_all'] = true;
		} elseif (Pw::strlen($array['content']) > 140) {
			$errcode['is_read_all'] = true;
			$array['content'] = Pw::substrs($array['content'], 140);
		}
		return $array;
	}
	
	/**
	 * 构建板块的链接
	 *
	 * @param int $fid
	 * @param string $fname
	 * @return string
	 */
	protected function _bulidFrom($fid, $fname) {
		return '版块&nbsp;-&nbsp;<a href="' . WindUrlHelper::createUrl('bbs/thread/run', array('fid' => $fid)) . '">' . $fname . '</a>';
	}
}

class PwTopicFresh extends PwBaseFresh {
	
	protected $_topic;
	protected $_forum;
	protected $_poll;
	protected $_att;

	public function __construct($ids) {
		$this->_topic = PwDataLazyLoader::getInstance('PwFreshContentFromTopic');
		$this->_forum = PwDataLazyLoader::getInstance('PwFreshContentFromForum');
		$this->_poll = PwDataLazyLoader::getInstance('PwFreshContentFromPoll');
		$this->_att = PwDataLazyLoader::getInstance('PwFreshContentFromAtt');
		$this->_topic->set($ids);
	}

	public function init() {
		$content = $this->_topic->fetch();
		$poll_ids = $att_ids = $forum_ids = array();
		foreach ($content as $key => $value) {
			if ($value['special'] == 1) $poll_ids[] = $value['tid'];
			if ($value['aids']) $att_ids[] = $value['tid'] . '_0';
			$forum_ids[] = $value['fid'];
		}
		$this->_forum->set($forum_ids);
		if ($poll_ids) $this->_poll->set($poll_ids);
		if ($att_ids) $this->_att->set($att_ids);
	}

	public function offer($id) {
		$topic = $this->_topic->fetchOne($id);
		$forum = $this->_forum->fetchOne($topic['fid']);
		$topic['pic'] = $topic['aids'] ? $this->_att->fetchOne($id . '_0') : array();
		$errcode = array();
		$topic = $this->_bulidContent($topic, $errcode);
		!$topic['word_version'] && $topic['content'] = Wekit::load('SRV:word.srv.PwWordFilter')->replaceWord($topic['content'], $topic['word_version']);
		
		$result = array(
			'replies' => $topic['replies'],
			'like_count' => $topic['like_count'],
			'created_username' => $topic['created_username'],
			'title' => $topic['subject'],
			'content' => $topic['content'],
			'from' => $this->_bulidFrom($forum['fid'], $forum['name']),
			'pic' => $topic['pic']
		);
		if ($errcode) $result += $errcode;
		return $result;
	}
}

class PwReplyFresh extends PwBaseFresh {
	
	protected $_reply;
	protected $_topic;
	protected $_forum;
	protected $_att;

	protected $_relation = array();

	public function __construct($ids) {
		$this->_reply = PwDataLazyLoader::getInstance('PwFreshContentFromReply');
		$this->_topic = PwDataLazyLoader::getInstance('PwFreshContentFromTopic');
		$this->_forum = PwDataLazyLoader::getInstance('PwFreshContentFromForum');
		$this->_att = PwDataLazyLoader::getInstance('PwFreshContentFromAtt');
		$this->_reply->set($ids);
	}

	public function init() {
		$content = $this->_reply->fetch();
		$forum_ids = array();
		foreach ($content as $key => $value) {
			$this->_relation[$key] = $value['tid'];
			$forum_ids[] = $value['fid'];
			if ($value['aids']) $att_ids[] = $value['tid'] . '_' . $value['pid'];
		}
		$this->_forum->set($forum_ids);
		$this->_topic->set(array_values($this->_relation));
		if ($att_ids) $this->_att->set($att_ids);
	}

	public function offer($id) {
		$reply = $this->_reply->fetchOne($id);
		$forum = $this->_forum->fetchOne($reply['fid']);
		$quote = $this->_topic->fetchOne($this->_relation[$id]);
		$from  = $this->_bulidFrom($forum['fid'], $forum['name']);
		$reply['pic'] = $reply['aids'] ? $this->_att->fetchOne($reply['tid'] . '_' . $id) : array();
		$reply = $this->_bulidContent($reply, $errcode);
		$quote = $this->_bulidContent($quote, $_tmp);
		!$reply['word_version'] && $reply['content'] = Wekit::load('SRV:word.srv.PwWordFilter')->replaceWord($reply['content'], $reply['word_version']);
		
		$result = array(
			'replies' => $reply['replies'],
			'like_count' => $reply['like_count'],
			'created_username' => $reply['created_username'],
			'content' => $reply['content'],
			'from' => $from,
			'pic' => $pic,
			'quote' => array(
				//'id' => $id,
				'tid' => $quote['tid'],
				'type' => PwFresh::TYPE_THREAD_TOPIC,
				'src_id' => $quote['tid'],
				'replies' => $quote['replies'],
				'like_count' => $quote['like_count'],
				'created_userid' => $quote['created_userid'],
				'created_username' => $quote['created_username'],
				'created_time' => $quote['created_time'],
				'subject' => $quote['subject'],
				'content' => $quote['content'],
				'url' => WindUrlHelper::createUrl('bbs/read/run', array('tid' => $quote['tid'])),
				'from' => $from
			)
		);
		if ($errcode) $result += $errcode;
		return $result;
	}
}

class PwWeiboFresh extends PwBaseFresh {
	
	protected $_weibo;
	protected $_relation = array();
	protected $_from = array(
		0 => array('新鲜事'),
		PwWeibo::TYPE_MEDAL => array('勋章', 'medal/index/run'),
		PwWeibo::TYPE_LIKE => array('喜欢', 'like/like/run'),
	);

	public function __construct($ids) {
		$this->_weibo = PwDataLazyLoader::getInstance('PwFreshContentFromWeibo');
		$this->_weibo->set($ids);
	}

	public function init() {
		$ids = array();
		$arr = $this->_weibo->fetch();
		foreach ($arr as $key => $value) {
			if ($value['src_id'] && !isset($arr[$value['src_id']])) {
				$ids[] = $value['src_id'];
			}
		}
		if ($ids) $this->_weibo->set($ids);
	}

	public function offer($id) {
		$weibo = $this->_weibo->fetchOne($id);
		$weibo['useubb'] = 1;
		$weibo = $this->_bulidContent($weibo, $errcode);
		$result = array(
			'replies' => $weibo['comments'],
			'like_count' => $weibo['like_count'],
			'created_username' => $weibo['created_username'],
			'content' => $weibo['content'],
			'from' => $this->_bulidFrom($weibo['type'], '')
		);
		if ($weibo['src_id']) {
			$quote = $this->_weibo->fetchOne($weibo['src_id']);
			$quote['useubb'] = 1;
			$quote = $this->_bulidContent($quote, $_tmp);
			$result['quote'] = array(
				//'id' => $id,
				'type' => PwFresh::TYPE_WEIBO,
				'src_id' => $quote['weibo_id'],
				'replies' => $quote['comments'],
				'like_count' => $quote['like_count'],
				'created_userid' => $quote['created_userid'],
				'created_username' => $quote['created_username'],
				'created_time' => $quote['created_time'],
				'content' => $quote['content'],
				'url' => WindUrlHelper::createUrl('space/index/fresh', array('uid' => $quote['created_userid'], 'weiboid' => $quote['weibo_id'])),
				'from' => $this->_bulidFrom($quote['type'], '')
			);
		}
		//if ($errcode) $result += $errcode;
		return $result;
	}

	/* (non-PHPdoc)
	 * @see PwBaseFresh::_bulidFrom()
	 */
	protected function _bulidFrom($fid, $name) {
		$from = isset($this->_from[$fid]) ? $this->_from[$fid] : $this->_from[0];
		if ($from[1]) return '<a href="' . WindUrlHelper::createUrl($from[1]) . '">' . $from[0] . '</a>';
		return $from[0];
	}
}

class PwFreshContentFromTopic implements iPwDataSource2 {

	public function getData($ids) {
		return Wekit::load('forum.PwThread')->fetchThread($ids, PwThread::FETCH_ALL);
	}
}

class PwFreshContentFromReply implements iPwDataSource2 {
	
	public function getData($ids) {
		return Wekit::load('forum.PwThread')->fetchPost($ids);
	}
}

class PwFreshContentFromForum implements iPwDataSource2 {
	
	public function getData($ids) {
		return Wekit::load('forum.PwForum')->fetchForum($ids);
	}
}

class PwFreshContentFromPoll implements iPwDataSource2 {
	
	public function getData($ids) {
		return array();
	}
}

class PwFreshContentFromWeibo implements iPwDataSource2 {
	
	public function getData($ids) {
		return Wekit::load('weibo.PwWeibo')->getWeibos($ids);
	}
}

class PwFreshContentFromAtt implements iPwDataSource2 {
	
	public function getData($ids) {
		$tids = $pids = array();
		foreach ($ids as $key => $value) {
			list($tid, $pid) = explode('_', $value);
			$tids[] = $tid;
			$pids[] = $pid;
		}
		$result = array();
		$array = Wekit::load('attach.PwThreadAttach')->fetchAttachByTidAndPid($tids, $pids);
		foreach ($array as $key => $value) {
			if ($value['type'] != 'img' || ($value['special'] > 0 && $value['cost'] > 0)) continue;
			$_key = $value['tid'] . '_' . $value['pid']; 
			$result[$_key][$value['aid']] = $value;
		}
		return $result;
	}
}