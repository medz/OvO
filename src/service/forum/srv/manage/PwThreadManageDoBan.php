<?php
Wind::import('SRV:forum.srv.manage.PwThreadManageDo');

/**
 * 帖子-禁止
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadManageDoBan.php 23904 2013-01-17 05:27:48Z xiaoxia.xuxx $
 * @package src.service.forum.srv.manage
 */
class PwThreadManageDoBan extends PwThreadManageDo {
	
	protected $tids;
	protected $delete = array();
	protected $banInfo = array();
	
	private $selectBanUsers = array();
	private $threadCreatedUids = array();
	
	/**
	 * 获得用户权限
	 * 0：没权限
	 * 1：全局
	 * 2：本版
	 * 
	 * @var int
	 */
	private $right = 0;
	
	/**
	 * 当前登录用户的Bo
	 * 
	 * @var PwUserBo
	 */
	private $loginUser = null;
	
	/**
	 * 构造方法
	 *
	 * @param PwThreadManage $srv
	 * @param PwUserBo $bo
	 */
	public function __construct(PwThreadManage $srv, PwUserBo $bo) {
		parent::__construct($srv);
		$this->loginUser = $bo;
	}

	/* (non-PHPdoc)
	 * @see PwThreadManageDo::check()
	 */
	public function check($permission) {
		if (!isset($permission['ban']) || !$permission['ban']) return false;
		//管理组的用户不能被禁言
		$users = $this->getBanUsers();
		$_tmp = array();
		foreach ($users as $item) {
			$item['groupid'] > 0 && $_tmp[] = $item['groupid'];
		}
		if ($_tmp && false === $this->canBan($_tmp)) {
			return new PwError('USER:ban.banuser.forbidden');
		}
		return true;
	}

	/* (non-PHPdoc)
	 * @see PwThreadManageDo::gleanData()
	 */
	public function gleanData($value) {
		$this->tids[] = $value['tid'];
		$this->threadCreatedUids[] = $value['created_userid'];
	}
	
	/* (non-PHPdoc)
	 * @see PwThreadManageDo::run()
	 */
	public function run() {
		list($banDmList, $_notice) = $this->_buildBanDm();
		/* @var $service PwUserBanService */
		$service = Wekit::load('user.srv.PwUserBanService');
		$r = $service->banUser($banDmList);
		if ($r instanceof PwError) return $r;
		if ($this->banInfo->sendNotice) {
			$service->sendNotice($_notice);
		}
		$this->_delThreads();
		Wekit::load('log.srv.PwLogService')->addBanUserLog($this->loginUser, $this->banInfo->uids, $this->banInfo->types, $this->banInfo->reason, $this->banInfo->end_time);
		return true;
	}

	/**
	 * 获得帖子的发表者
	 * 
	 * @return array
	 */
	public function getBanUsers() {
		if ($this->selectBanUsers) return $this->selectBanUsers;
		$users = array();
		foreach ($this->srv->getData() as $key => $value) {
			$users[] = $value['created_userid'];
		}
		$this->selectBanUsers = Wekit::load('user.PwUser')->fetchUserByUid($users);
		return $this->selectBanUsers;
	}
	
	/**
	 * 判断是否有权限
	 * 删除全站或是本版帖子
	 * 
	 * @return int
	 */
	public function getRight() {
		if ($this->right) return $this->right;
		$this->right = array('delCurrentThread' => 0, 'delForumThread' => 0, 'delSiteThread' => 0);
		$permission = $this->loginUser->getPermission('operate_thread', false, array());
		//如果是论坛斑竹,并且是操作的是自己的版块的帖子，则有删除选择，否则没有删除本版权限
		if (isset($permission['delete']) && 1 == $permission['delete']) {
			$this->right['delCurrentThread'] = 1;
			$this->right['delSiteThread'] = 1;
		} elseif (5 == $this->loginUser->gid && $this->srv->isBM($this->srv->getFids())) {
			$permission = $this->loginUser->getPermission('operate_thread', true, array());
			if (isset($permission['delete']) && $permission['delete'] == 1) {
				$this->right['delCurrentThread'] = 1;
				$this->right['delForumThread'] = 1;
			}
		}
		//如果所选用户不是全都是帖子发帖者，则删除当前帖子不可选
		if (1 == $this->right['delCurrentThread']) {
			$threadUids = array();
			foreach ($this->srv->getData() as $_item) {
				$threadUids[] = $_item['created_userid'];
			}
			if (array_diff(array_keys($this->getBanUsers()), $threadUids)) {
				$this->right['delCurrentThread'] = 0;
			}
		}
		return $this->right;
	}
	
	/**
	 * 设置禁止设置
	 *
	 * @param array $dmList
	 * @return PwThreadManageDoBan
	 */
	public function setBanInfo($banInfo) {
		$this->banInfo = $banInfo;
		return $this;
	}
	
	/**
	 * 设置禁止的用户ID
	 *
	 * @param array $uids
	 * @return PwThreadManageDoBan
	 */
	public function setBanUids($uids) {
		$this->selectBanUsers = Wekit::load('user.PwUser')->fetchUserByUid(is_array($uids) ? $uids : array($uids));
		return $this;
	}
	
	/**
	 * 删除操作
	 *
	 * @param array $deletes
	 * @return PwThreadManageDoBan
	 */
	public function setDeletes($deletes) {
		$this->delete = $deletes;
		return $this;
	}
	
	/**
	 * 管理组下的用户组不允许被禁止
	 *
	 * @param array $groupid
	 */
	public function canBan($groupid) {
		$systemGroups = Wekit::load('usergroup.PwUserGroups')->getGroupsByType('system');
		return array_intersect((array)$groupid, array_keys($systemGroups)) ? false : true;
	}
	
	/**
	 * 构建禁止的对象
	 * 
	 * @return array
	 */
	private function _buildBanDm() {
		Wind::import('SRV:user.dm.PwUserBanInfoDm');
		Wind::import('SRV:user.PwUserBan');
		$rightTypes = array(PwUserBan::BAN_AVATAR, PwUserBan::BAN_SIGN, PwUserBan::BAN_SPEAK);
		
		if ($this->banInfo->end_time > 0) $this->banInfo->end_time = Pw::str2time($this->banInfo->end_time);
		$data = $_notice = array();
		foreach ($this->banInfo->types as $type) {
			if (!in_array($type, $rightTypes)) continue;
			foreach ($this->selectBanUsers as $uid => $_item) {
				$dm = new PwUserBanInfoDm();
				$dm->setUid($uid)
					->setCreateTime(Pw::getTime())
					->setCreatedUid($this->loginUser->uid)
					->setOperator($this->loginUser->username)
					->setEndTime(intval($this->banInfo->end_time))
					->setTypeid($type)
					->setReason($this->banInfo->reason)
					->setFid(0);
				$data[] = $dm;
				
				isset($_notice[$uid]) || $_notice[$uid] = array();
				$_notice[$uid]['end_time'] = $this->banInfo->end_time;
				$_notice[$uid]['reason'] = $this->banInfo->reason;
				$_notice[$uid]['type'][] = $type;
				$_notice[$uid]['operator'] = $this->loginUser->username;
				
			}
		}
		return array($data, $_notice);
	}
	
	/**
	 * 删除帖子
	 *
	 * @param array $param
	 * @return boolean
	 */
	private function _delThreads() {
		Wind::import('SRV:forum.srv.operation.PwDeleteTopic');
		$right = $this->getRight();
		$banUids = array_keys($this->getBanUsers());
		//【用户禁止帖子删除】
		//删除当前主题帖子  当禁止非楼主时，不能删除当前主题
		if (1 == $this->delete['current'] && 1 === $right['delCurrentThread'] && !array_diff($banUids, $this->threadCreatedUids)) {
			Wind::import('SRV:forum.srv.dataSource.PwFetchTopicByTid');
			//【用户禁止帖子删除】-根据帖子ID列表删除帖子到回收站
			$service = new PwDeleteTopic(new PwFetchTopicByTid($this->tids), $this->loginUser);
			$service->setRecycle(true)->setIsDeductCredit(true)->execute();
		}
		if (1 == $this->delete['site'] && 1 === $right['delSiteThread']) {
			Wind::import('SRV:forum.srv.dataSource.PwFetchTopicByUid');
			//【用户禁止帖子删除】-并且按照用户ID列表删除帖子到回收站
			$service = new PwDeleteTopic(new PwFetchTopicByUid($banUids), $this->loginUser);
			$service->setRecycle(true)->setIsDeductCredit(true)->execute();
		} elseif (1 == $this->delete['forum'] && 1 === $right['delForumThread']) {
			Wind::import('SRV:forum.srv.dataSource.PwFetchTopicByFidAndUids');
			//【用户禁止帖子删除】-并且按照用户ID列表+版块ID删除帖子到回收站
			foreach ($this->srv->getFids() as $fid) {
				$service = new PwDeleteTopic(new PwFetchTopicByFidAndUids($fid, $banUids), $this->loginUser);
				$service->setRecycle(true)->setIsDeductCredit(true)->execute();
			}
		}
		return true;
	}
}