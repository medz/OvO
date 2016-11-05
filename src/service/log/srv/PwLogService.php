<?php
Wind::import('SRV:log.dm.PwLogDm');

/**
 * log日志的服务
 *
 * @author xiaoxia.xu<xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwLogService.php 23697 2013-01-15 05:17:30Z jieyin $
 * @package src.service.log.srv
 */
class PwLogService {
	
	/**
	 * 添加话题屏蔽的LOG
	 *
	 * @param PwUserBo $user
	 * @param PwLogDm $dm
	 * @param array $langArgs
	 * @param boolean $ifShield
	 * @return boolean
	 */
	public function addShieldTagLog(PwUserBo $user, PwLogDm $dm, $langArgs, $ifShield = true) {
		$dm->setCreatedUser($user->uid, $user->username)
			->setCreatedTime(Pw::getTime())
			->setIp(Wind::getComponent('request')->getClientIp())
			->setTypeid($this->getOperatTypeid($ifShield ? 'shieldtag' : 'unshieldtag'));
		$lang = 'LOG:shield.tag.message';
		!$ifShield && $lang = 'LOG:unshield.tag.message';
		$_args = array();
		$_args['{tagtitle}'] = sprintf('<a href="%s" target="_blank">%s</a>', $langArgs['tag_url'], $this->_buildSecurity($langArgs['tag']));
		$_args['{type}'] = $this->_buildSecurity($langArgs['type']);
		if (isset($langArgs['content_url']) && $langArgs['content_url']) {
			$_args['{content}'] = sprintf('<a href="%s" target="_blank">%s</a>', $langArgs['content_url'], $this->_buildSecurity($langArgs['content']));
		} else {
			$_args['{content}'] = $this->_buildSecurity($langArgs['content']);
		}
		$dm->setContent($this->getLogMsg($lang, $_args));
		$this->_getLogDs()->addLog($dm);
		return true;
	}
	
	/**
	 * 添加编辑帖子的LOG
	 *
	 * @param PwUserBo $user
	 * @param array $thread
	 * @param boolean $isReply
	 * @return boolean
	 */
	public function addEditThreadLog(PwUserBo $user, $thread, $isReply = false) {
		if (!$thread) return false;
		$langArgs = array();
		$langArgs['{createdUser}'] = sprintf('<a href="%s" target="_blank">%s</a>', WindUrlHelper::createUrl('space/index/run', array('uid' => $user->uid)), $user->username);
		$msg = '';
		if ($isReply) {
			$langArgs['{title}'] = sprintf('<a href="%s" target="_blank">%s</a>', WindUrlHelper::createUrl('bbs/read/run', array('tid' => $thread['tid']), $thread['pid']), $this->_buildSecurity($thread['subject']));
			$msg = 'LOG:editThread.reply.message';
		} else {
			$langArgs['{title}'] = sprintf('<a href="%s" target="_blank">%s</a>', WindUrlHelper::createUrl('bbs/read/run', array('tid' => $thread['tid'])), $this->_buildSecurity($thread['subject']));
			$msg = 'LOG:editThread.message';
		}
		$dm = new PwLogDm();
		$dm->setFid($thread['fid'])
			->setTid($thread['tid'])
			->setPid($isReply ? $thread['pid'] : '')
			->setContent($this->getLogMsg($msg, $langArgs))
			->setCreatedTime(Pw::getTime())
			->setCreatedUser($user->uid, $user->username)
			->setOperatedUser($thread['created_userid'], $thread['created_username'])
			->setIp(Wind::getComponent('request')->getClientIp())
			->setTypeid($this->getOperatTypeid('edit'));
		$this->_getLogDs()->addLog($dm);
		return true;
	}
	
	/**
	 * 删除帖子附件
	 *
	 * @param PwUserBo $user
	 * @param array $attach
	 * @return boolean
	 */
	public function addDeleteAtachLog(PwUserBo $user, $attach) {
		if (!$attach) return false;
		$langArgs = array();
		$langArgs['{createdUser}'] = sprintf('<a href="%s" target="_blank">%s</a>', WindUrlHelper::createUrl('space/index/run', array('uid' => $user->uid)), $user->username);
		$langArgs['{attach}'] = sprintf('<a href="%s" target="_blank">%s</a>', WindUrlHelper::createUrl('bbs/read/run', array('tid' => $attach['tid']), $attach['pid'] ? $attach['pid'] : ''), $this->_buildSecurity($attach['name']));
		$_createdUser = Wekit::load('user.PwUser')->getUserByUid($attach['created_userid']);
		$dm = new PwLogDm();
		$dm->setFid($attach['fid'])
			->setTid($attach['tid'])
			->setPid($attach['pid'] ? $attach['pid'] : '')
			->setExtends($attach['name'])
			->setContent($this->getLogMsg('LOG:delete.attach.message', $langArgs))
			->setCreatedTime(Pw::getTime())
			->setCreatedUser($user->uid, $user->username)
			->setOperatedUser($attach['created_userid'], $_createdUser['username'])
			->setIp(Wind::getComponent('request')->getClientIp())
			->setTypeid($this->getOperatTypeid('delatc'));
		$this->_getLogDs()->addLog($dm);
		return true;
	}
	
	/**
	 * 添加前台帖子管理的相关日志
	 *
	 * @param PwUserBo $user 操作者
	 * @param string $type 操作类型
	 * @param array $threads 被操作的帖子列表
	 * @param string $reason 操作原因
	 * @param string $extends 
	 * @param boolean $useReplyMsg 是否使用回复的管理日志格式
	 */
	public function addThreadManageLog(PwUserBo $user, $type, $threads, $reason, $extends = '', $useReplyMsg = false) {
		if (!$threads) return false;
		$typeid = $this->getOperatTypeid($type);
		$typeTitle = $this->getOperatTypeTitle($type);
		$_logDms = $langArgs = array();
		$langArgs['{operattype}'] = $typeTitle;
		$langArgs['{reason}'] = $reason ? $this->_buildSecurity($reason) : '无';
		$_logMsg = $useReplyMsg ? 'LOG:thread.reply.message' : 'LOG:thread.manage.message';
		Wind::import('SRV:forum.dm.PwTopicDm');
		foreach ($threads as $thread) {
			$langArgs['{title}'] = sprintf('<a href="%s" target="_blank">%s</a>', WindUrlHelper::createUrl('bbs/read/run', array('tid' => $thread['tid'])), $this->_buildSecurity($thread['subject']));
			$langArgs['{createdUser}'] = sprintf('<a href="%s" target="_blank">%s</a>', WindUrlHelper::createUrl('space/index/run', array('uid' => $user->uid)), $user->username);
			$_dm = new PwLogDm();
			$_dm->setCreatedTime(Pw::getTime())
			->setCreatedUser($user->uid, $user->username)
			->setOperatedUser($thread['created_userid'], $thread['created_username'])
			->setIp(Wind::getComponent('request')->getClientIp())
			->setExtends($extends)
			->setFid($thread['fid'])
			->setTid($thread['tid'])
			->setPid((isset($thread['pid']) && $thread['pid']) ? $thread['pid'] : 0)
			->setTypeid($typeid)
			->setContent($this->getLogMsg($_logMsg, $langArgs));
			if (!isset($thread['pid']) && !Pw::getstatus($thread['tpcstatus'], PwThread::STATUS_OPERATORLOG)) {
				$topicDm = new PwTopicDm($thread['tid']);
				$topicDm->setOperatorLog(true);
				Wekit::load('forum.PwThread')->updateThread($topicDm, PwThread::FETCH_MAIN);
			}
			
			$_logDms[] = $_dm;
		}
		$this->_getLogDs()->batchAddLog($_logDms);
		return true;
	}
	
	/**
	 * 添加禁止用户的相关LOG
	 *
	 * @param PwUserBo $user
	 * @param array $uids 禁止用户
	 * @param array $types 禁止类型
	 * @param string $reason
	 * @param string $endTime
	 * @return boolean
	 */
	public function addBanUserLog(PwUserBo $user, $uids, $types, $reason, $endTime = '') {
		if (!$types) return false;
		$userList = Wekit::load('user.PwUser')->fetchUserByUid($uids);
		if (!$userList) return false;
		$_logDms = array();
		foreach ($types as $_type) {
			if ($_type == 1) {
				$type = 'banuserspeak';
			} elseif ($_type == 2) {
				$type = 'banuseravatar';
			} elseif ($_type == 4) {
				$type = 'banusersign';
			} else {
				continue;
			}
			$typeid = $this->getOperatTypeid($type);
			$title = $this->getOperatTypeTitle($type);
			$langArgs = array(
				'{operatedUser}' => '',
				'{createdUser}' => sprintf('<a href="%s" target="_blank">%s</a>', WindUrlHelper::createUrl('space/index/run', array('uid' => $user->uid)), $user->username),
				'{operattype}' => $title,
				'{reason}' => $reason ? $this->_buildSecurity($reason) : '无');
			foreach ($userList as $_uid => $_user) {
				$langArgs['{operatedUser}'] = sprintf('<a href="%s" target="_blank">%s</a>', WindUrlHelper::createUrl('space/index/run', array('uid' => $_uid)), $_user['username']);
				$_dm = new PwLogDm();
				$_dm->setCreatedTime(Pw::getTime())
					->setCreatedUser($user->uid, $user->username)
					->setOperatedUser($_uid, $_user['username'])
					->setIp(Wind::getComponent('request')->getClientIp())
					->setExtends($endTime)
					->setTypeid($typeid)
					->setContent($this->getLogMsg('LOG:operated.message', $langArgs));
				$_logDms[] = $_dm;
			}
		}
		$this->_getLogDs()->batchAddLog($_logDms);
		return true;
	}
	
	/**
	 * 添加删除新鲜事时的管理日志
	 *
	 * @param PwUserBo $user
	 * @param array $data
	 * @return boolean
	 */
	public function addDeleteFreshLog(PwUserBo $user, $data) {
		if (!$data) return false;
		$typeid = $this->getOperatTypeid('delfresh');
		$typeTitle = $this->getOperatTypeTitle('delfresh');
		$_logDms = array();
		foreach ($data as $_item) {
			$title = Pw::substrs(strip_tags(Pw::stripWindCode($_item['content'])), 20, 0, true);
			$_dm = new PwLogDm();
			$_dm->setCreatedTime(Pw::getTime())
			->setCreatedUser($user->uid, $user->username)
			->setOperatedUser($_item['created_userid'], $_item['created_username'])
			->setIp(Wind::getComponent('request')->getClientIp())
			->setExtends($_item['weibo_id'])
			->setTypeid($typeid)
			->setContent($this->getLogMsg('LOG:delete.fresh.message', array('{title}' => "'" . $title . "'")));
			$_logDms[] = $_dm;
		}
		$this->_getLogDs()->batchAddLog($_logDms);
		return true;
	}
	
	/**
	 * 前台管理日志搜索接口
	 *
	 * @param PwLogSo $so
	 * @param int $limit
	 * @param int $start
	 * @return array(logList, forumList)
	 */
	public function searchManageLogs(PwLogSo $so, $limit = 10, $offset = 0) {
		$list = $this->_getLogDs()->search($so, $limit, $offset);
		$types = array_flip($this->getOperatTypeid());
		foreach ($list as $key => $value) {
			$list[$key]['type'] = $this->getOperatTypeTitle($types[$value['typeid']]);
		}
		return $list;
	}
	
	/**
	 * 根据tid获得帖子的管理日志列表
	 *
	 * @param int $tid
	 * @param int $limit 
	 * @param int $start
	 * @return array
	 */
	public function getThreadLog($tid, $limit = 10, $start = 0) {
		$list = $this->_getLogDs()->getLogBytid($tid, 0, $limit, $start);
		$types = array_flip($this->getOperatTypeid());
		foreach ($list as $key => $value) {
			if (!in_array($types[$value['typeid']], array('topped', 'catetopped','sitetopped','highlight'))) {
				$list[$key]['extends'] = '';
			}
			$list[$key]['type'] = $this->getOperatTypeTitle($types[$value['typeid']]);
		}
		return $list;
	}
	
	/**
	 * 获得日志的信息
	 *
	 * @param string $msg
	 * @param array $args
	 * @return string
	 */
	protected function getLogMsg($msg, $args) {
		static $lang = null;
		if (null == $lang) {
			$lang = Wind::getComponent('i18n');
		}
		return $lang->getMessage($msg, $args);
	}
	
	/**
	 * 获得当前编码
	 *
	 * @return string
	 */
	protected function _buildSecurity($content) {
		static $charset = '';
		if ($charset) {
			$charset = Wekit::V('charset') == 'GBK' ? 'ISO-8859-1' : Wekit::V('charset');
		}
		return WindSecurity::escapeHTML($content, $charset);
	}
	
	/**
	 * 获取操作类型
	 * 
	 * @param string $t
	 * @return array
	 */
	public function getOperatTypeid($t = '') {
		static $typeid = array(
			'degist' => 1,//加精
			'undegis' => 2,//取消加精
			'highlight' => 3,//加亮
			'type' => 4,//分类
			'move' => 5,//移动
			'readed' => 6,//已阅
			'edit' => 7,//编辑
			'copy' => 8,//复制
			'delete' => 9, //删除
			'lock' => 10, //锁定
			'unlock' => 11, //接触锁定
			'closed' => 12, //关闭
			'down' => 13, //压帖
			'shield' => 14, //屏蔽
			'unshield' => 15, //取消屏蔽
			'delatc' => 16, //删除附件
			'up' => 17,//提前
			'threadtopped' => 18, //帖内置顶
			'topped' => 19,//本版置顶
			'catetopped' => 20,//分类置顶
			'sitetopped' => 21,//全局置顶
			'untopped' => 22,//取消置顶
			'delfresh' => 25,//删除新鲜事
			'shieldtag' => 28,//话题屏蔽
			'unshieldtag' => 29, //取消话题屏蔽
			'banuserspeak' => 30,//禁言用户
			'banuseravatar' => 31,//禁止用户头像
			'banusersign' => 32,//禁止用户帖子签名
			'other' => 40, //其他
		);
		return $t ? $typeid[$t] : $typeid;
	}
	
	/**
	 * 获得操作项的名字
	 * 
	 * @param strin $type
	 * @return array
	 */
	public function getOperatTypeTitle($type = '') {
		$types = array(
			'degist' => '设为精华',
			'undegis' => '取消精华',
			'highlight' => '加亮',
			'type' => '分类',
			'move' => '移动',
			'readed' => '已阅',
			'edit' => '编辑',
			'copy' => '复制',
			'delete' => '删除',
			'lock' => '锁定',
			'unlock' => '解除锁定',
			'closed' => '关闭',
			'down' => '压帖',
			'shield' => '屏蔽',
			'unshield' => '取消屏蔽',
			'delatc' => '删除附件',
			'up' => '提前',
			'threadtopped' => '帖内置顶',
			'topped' => '本版置顶',
			'catetopped' => '分类置顶',
			'sitetopped' => '全局置顶',
			'untopped' => '取消置顶',
			'delfresh' => '新鲜事删除',
			'shieldtag' => '话题屏蔽',
			'unshieldtag' => '取消话题屏蔽',
			'banuserspeak' => '禁言用户',
			'banuseravatar' => '禁止头像',
			'banusersign' => '禁止帖子签名',
			'other' => '其他',
		);
		return $type ? $types[$type] : $types;
	}
	
	/**
	 * 获得日志的DS
	 *
	 * @return PwLog
	 */
	private function _getLogDs() {
		return Wekit::load('log.PwLog');
	}
	
	/**
	 * 获取前台登录错误的DS服务
	 *
	 * @return PwLogLogin
	 */
	private function _getLoginLogDs() {
		return Wekit::load('log.PwLogLogin');
	}
}