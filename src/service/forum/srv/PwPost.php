<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子发布流程
 *
 * -> 1.check 检查帖子发布运行环境
 * -> 2.appendDo(*) 增加帖子发布时的行为动作,例:投票、附件等(可选)
 * -> 3.execute 发布
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwPost.php 28950 2013-05-31 05:58:25Z jieyin $
 * @package forum
 */
class PwPost extends PwBaseHookService {

	public $action;
	public $forum;
	public $user;
	public $special; // 帖子类型

	public function __construct(PwPostAction $action) {
		$this->action = $action;
		$this->forum = $action->forum;
		$this->user = $action->user;
		$this->special = $action->getSpecial();
		
		/** hook **/
		$this->action->setSrv($this);
	}

	/**
	 * 发帖之前检测
	 *
	 * @return bool
	 */
	public function check() {
		if (($result = $this->isInit()) !== true) {
			return new PwError('data.error');
		}
		if (($result = $this->checkForum()) !== true) {
			return $result;
		}
		if (($result = $this->checkPost()) !== true) {
			return $result;
		}
		if ($this->isBan()) {
			return new PwError('ban');
		}
		if (($result = $this->action->check()) !== true) {
			return $result;
		}
		return true;
	}

	/**
	 * 初始化信息是否满足要求
	 *
	 * @return bool
	 */
	public function isInit() {
		return $this->action->isInit();
	}

	/**
	 * 检测是否拥有该版操作权限
	 *
	 * @return bool
	 */
	public function checkForum() {
		if (!$this->forum->isForum()) {
			return new PwError('BBS:post.forum.not.exists');
		}
		if (($result = $this->forum->allowVisit($this->user)) !== true) {
			return new PwError('BBS:forum.permissions.visit.allow', 
				array('{grouptitle}' => $this->user->getGroupInfo('name')));
		}
		return true;
	}

	/**
	 * 检测是否允许发帖
	 *
	 * @return bool
	 */
	public function checkPost() {
		if ($this->user->groupid == 7) {
			return new PwError('REG_CHECK');
		}
		/*
		$config = Wekit::C('bbs');
		if ($config['post.timing.open'] && !$this->user->inGroup($config['post.timing.groups']) && !self::inTime($config['post.timing.start_hour'], $config['post.timing.start_min'], $config['post.timing.end_hour'], $config['post.timing.end_min'])) {
			return new PwError('BBS:post.timing');
		}
		*/
		return true;
	}

	/**
	 * 检测用户是否被禁言
	 *
	 * @return bool
	 */
	public function isBan() {
		if ($this->user->gid == 6) {
			Wind::import('SRV:user.srv.PwBanBp');
			$banBp = new PwBanBp($this->user->uid);
			$memberid = 0;
			if (false === $banBp->checkIfBanSpeak()) {
				$memberid = $banBp->recoveryBanSpeaKError();
			} elseif ($banBp->endDateTimeBanSpeak()) {
				$memberid = $banBp->callEndDateTimeBanSpeak();
			}
			if ($memberid) {
				$this->user->info['groups'] = '';
				$this->user->info['groupid'] = 0;
				$this->user->info['memberid'] = $memberid;
				$this->user->groups = array($memberid);
				$this->user->resetGid($memberid);
				return false;
			}
			return true;
		}
		return false;
	}

	public function getDm() {
		return $this->action->getDm();
	}

	/**
	 * 各应用获取该用户dm来设置，以达到更新用户信息的目的
	 *
	 * @return object PwUserInfoDm
	 */
	public function getUserDm() {
		return $this->action->getUserDm();
	}

	public function getAttachs() {
		return $this->action->getAttachs();
	}

	/**
	 * 发布
	 *
	 * @param object $postDm 帖子数据模型
	 * @return bool
	 */
	public function execute(PwPostDm $postDm) {
		if (($result = $this->action->beforeRun($postDm)) instanceof PwError) {
			return $result;
		}
		if (($result = $this->action->dataProcessing($postDm)) !== true) {
			return $result;
		}
		if (($result = $this->action->execute()) !== true) {
			return $result;
		}
		$this->action->afterRun();
		$this->updateUser();
		return true;
	}

	public function getInfo() {
		return $this->action->getInfo();
	}

	public function getNewId() {
		return $this->action->getNewId();
	}

	public function getDisabled() {
		return $this->action->isDisabled();
	}

	/**
	 * 更新用户信息 /积分/发帖数/等
	 */
	public function updateUser() {
		Wind::import('SRV:credit.bo.PwCreditBo');
		$credit = PwCreditBo::getInstance();
		if ($operation = $this->action->getCreditOperate()) {
			$credit->operate($operation, $this->user, true, 
				array('forumname' => $this->forum->foruminfo['name']), 
				$this->forum->getCreditSet($operation));
		}
		$credit->execute();
		$this->action->updateUser();
		if ($userDm = $this->action->getUserDm(false)) {
			Wekit::load('user.PwUser')->editUser($userDm, PwUser::FETCH_DATA);
		}
	}

	public function appendDo($do) {
		$this->action->appendDo($do);
	}
	
	public function runDo($method) {
		$args = func_get_args();
		call_user_func_array(array($this->action, 'runDo'), $args);
	}

	public function getHookKey() {
		return $this->action->getHookKey();
	}

	/**
	 * 判断当前的时间，是否在允许的时间段内
	 *
	 * @param int $startHour 开始时间/小时
	 * @param int $startMin 开始时间/分钟
	 * @param int $endHour 结束时间/小时
	 * @param int $endMin 结束时间/分钟
	 * @return bool
	 */
	public static function inTime($startHour, $startMin, $endHour, $endMin) {
		list($currentHour, $currentMin) = explode(':', Pw::time2str(Pw::getTime(), 'H:i'));
		$startTime = self::times($startHour, $startMin);
		$endTime = self::times($endHour, $endMin);
		$currentTime = self::times($currentHour, $currentMin);
		if ($startTime == $endTime && $currentTime != $startTime) {
			return false;
		}
		if ($startTime < $endTime && $currentTime < $startTime || $currentTime > $endTime) {
			return false;
		}
		if ($startTime > $endTime && $currentTime > $endTime && $currentTime < $startTime) {
			return false;
		}
		return true;
	}

	public static function times($hour, $min) {
		return $hour * 60 + $min;
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseHookService::_getInterfaceName()
	 */
	protected function _getInterfaceName() {
		return '';
	}
}