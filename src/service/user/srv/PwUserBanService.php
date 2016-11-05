<?php
Wind::import('SRV:user.dm.PwUserBanInfoDm');
Wind::import('SRV:user.PwUserBan');
/**
 * 用户禁止服务
 * 用户行为禁止：
 * avatar: 禁止用户使用头像
 * sign： 禁止用户使用签名
 * speak：禁止用户发言
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserBanService.php 23904 2013-01-17 05:27:48Z xiaoxia.xuxx $
 * @package src.service.user.srv
 */
class PwUserBanService {
	
	/**
	 * 根据用户名/用户ID批量禁止用户
	 *
	 * @param array $dmList PwUserBanInfoDm 列表
	 * @return boolean
	 */
	public function banUser($dmList) {
		$banTypes = $this->getBanType();
		foreach ($dmList as $_dm) {
			if (!$_dm instanceof PwUserBanInfoDm) continue;
			if (true !== ($r = $_dm->beforeAdd())) return $r;
			$r = $this->_getDs()->addBanInfo($_dm);
			if ($r instanceof PwError) return $r;
			
			//操作相关类型的后续操作
			$class = Wekit::load($banTypes[$_dm->getField('typeid')]['class']);
			$r = call_user_func_array(array($class, 'afterBan'), array($_dm));
			if ($r instanceof PwError) return $r;
		}
		return true;
	}
	
	/**
	 * 根据条件检索
	 *
	 * @param PwUserBanSo $searchDo 
	 * @param int $limit  返回条数
	 * @param int $start  开始位置
	 * @return array
	 */
	public function searchBanInfo(PwUserBanSo $searchDo, $limit = 10, $start = 0) {
		$result = array();
		$list = $this->_getDs()->searchBanInfo($searchDo, $limit, $start);
		foreach ($list as $id => $item) {
			$result[$item['id']] = $this->_buildList($item);
		}
		return $result;
	}
	
	/**
	 * 解除禁止操作
	 *
	 * @param array $ids
	 * @return boolean
	 */
	public function batchDelete($ids) {
		$list = $this->_getDs()->fetchBanInfo($ids);
		$banTypes = $this->getBanType();
		$clearIds = array();
		foreach ($list as $_item) {
			$clearIds[] = $_item['id'];
			//操作相关类型的后续操作
			$class = Wekit::load($banTypes[$_item['typeid']]['class']);
			call_user_func_array(array($class, 'deleteBan'), array($_item['uid']));
		}
		$r = $this->_getDs()->batchDelete($clearIds);
		return $r instanceof PwError ? $r : $list;
	}
	
	/**
	 * 获得禁止类型
	 * //TODO 【用户禁止】禁止类型扩展
	 * @return array
	 */
	public function getBanType() {
		$types = array(
			1 => array('title' => '禁止发布', 'class' => 'SRV:user.srv.bantype.PwUserBanSpeak'),
			2 => array('title' => '禁止头像', 'class' => 'SRV:user.srv.bantype.PwUserBanAvatar'),
			4 => array('title' => '禁止签名', 'class' => 'SRV:user.srv.bantype.PwUserBanSign'),
		);
		return $types;
	}

	/**
	 * 自动禁止用户
	 * 
	 * 用户积分有变动的时候执行
	 * 在用户s_PwUserDataDao_update处作为hook执行
	 *
	 * @param int $uid
	 * @param array $fields 
	 * @param array $increaseFields 
	 * @return array
	 */
	public function autoBan($uid, $fields, $increaseFields) {
		//[如果自动禁止没有开启]
		$config = Wekit::C('site');
		if (0 == $config['autoForbidden.open'] || !$config['autoForbidden.type']) return false;
		//[自动禁止积分依据]如果更新的积分没有在禁止积分范围内
		$credit = $config['autoForbidden.condition']['credit'];
		$key = 'credit' . $credit;
		if (!in_array($key, array_keys($fields)) && !in_array($key, array_keys($increaseFields))) {
			return false;
		}
		//[禁止积分依据有没有到达禁止条件]
		$userBo = new PwUserBo($uid);
		if ($userBo->getCredit($credit) >= $config['autoForbidden.condition']['num']) return false;
		//执行禁止操作
		$dmList = array();
		$endTime = $config['autoForbidden.day'] > 0 ? $config['autoForbidden.day'] * 24 *3600 + Pw::getTime() : 0;
		foreach ($config['autoForbidden.type'] as $type) {
			$banDm = new PwUserBanInfoDm();
			$banDm->setEndTime($endTime)
				->setTypeid($type)
				->setReason($config['autoForbidden.reason'])
				->setCreatedUid(0)
				->setUid($uid);
			$dmList[] = $banDm;
		}
		$this->banUser($dmList);
		//发送消息
		$_notice = array($uid => array('end_time' => $endTime, 'reason' => $config['autoForbidden.reason'], 'type' => $config['autoForbidden.type'], 'operator' => 'system'));
		$this->sendNotice($_notice, 1);
		return true;
	}

	/**
	 * 发送消息
	 *
	 * @param array $notice
	 * @param int $type 消息类型《1：禁止，2：解禁，3：自动解禁》
	 * @return boolean
	 */
	public function sendNotice($bans, $type = 1) {
		/* @var $notice PwNoticeService */
		$notice = Wekit::load('SRV:message.srv.PwNoticeService');
		$banTypes = $this->getBanType();
		foreach ($bans as $uid => $_item) {
			$extends = array();
			$extends['operator'] = $_item['operator'];
			foreach ($_item['type'] as $_i) {
				isset($banTypes[$_i]) && $extends['type'][] = $banTypes[$_i]['title'];
			}
			if (!$extends['type']) continue;
			$extends['end_time'] = $_item['end_time'];
			$extends['created_time'] = $_item['created_time'];
			$extends['reason'] = $_item['reason'];
			$extends['ban'] = $type;
			$notice->sendNotice($uid, 'ban', $uid, $extends);
		}
		return true;
	}
	
	/**
	 * 组装输出列表信息
	 *
	 * @param array $item
	 * @return array
	 */
	private function _buildList($item) {
		$banTypes = $this->getBanType();
		/* @var $obj PwForum */
		$obj = Wekit::load('forum.PwForum');
		$type = $banTypes[$item['typeid']];
		$item['type'] = $type['title'];
		$item['child'] = '全局';
		if ($item['fid']) {
			/* @var $class PwUserBanTypeInterface */
			$class = Wekit::load($banTypes[$item['typeid']]['class']);
			$item['child'] = $class->getExtension($item['fid']);
		}
		/* @var $userDs PwUser */
		$userDs = Wekit::load('user.PwUser');
		$list = $userDs->fetchUserByUid(array($item['created_userid'], $item['uid']), PwUser::FETCH_MAIN);
		$item['created_username'] = $item['created_userid'] == 0 ? 'system' : $list[$item['created_userid']]['username'];
		$item['username'] = $list[$item['uid']]['username'];
		return $item;
	}
	
	/** 
	 * 获得用户禁止
	 *
	 * @return PwUserBan
	 */
	private function _getDs() {
		return Wekit::load('user.PwUserBan');
	}
}