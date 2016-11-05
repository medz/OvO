<?php

Wind::import('SRV:user.dm.PwUserInfoDm');

/**
 * 用户关注服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwAttentionService.php 22660 2012-12-26 07:45:31Z jinlong.panjl $
 * @package src.service.user.srv
 */
class PwAttentionService {
	
	/**
	 * 获取用户所有的分类(包括默认分类)
	 *
	 * @param int $uid
	 * @return array
	 */
	public function getAllType($uid) {
		$type = array();
		if (!$result = $this->_getTypeDs()->getTypeByUid($uid)) {
			return $type;
		}
		foreach ($result as $key => $value) {
			$type[$key] = $value['name'];
		}
		return $type;
	}
	
	/**
	 * 获取该用户所关注用户的分类
	 *
	 * @param int $uid
	 * @param array $touids
	 * @return array
	 */
	public function getUserType($uid, $touids) {
		$data = array();
		$result = $this->_getTypeDs()->getUserType($uid, $touids);
		foreach ($result as $key => $value) {
			$data[$value['touid']][] = $value['typeid'];
		}
		return $data;
	}

	/**
	 * 用户(A)关注了用户(B)
	 *
	 * @param int $uid 用户A
	 * @param int $touid 用户B
	 * @return bool| object PwError()
	 */
	public function addFollow($uid, $touid) {
		if (($result = $this->_getAttention()->addFollow($uid, $touid)) instanceof PwError) {
			return $result;
		}
		$user = $this->_getUser();
		$dm = new PwUserInfoDm($uid);
		$dm->addFollows(1);
		$user->editUser($dm, PwUser::FETCH_DATA);
		$dm = new PwUserInfoDm($touid);
		$dm->addFans(1);
		$user->editUser($dm, PwUser::FETCH_DATA);

		if ($fresh = $this->_getFresh()->getFreshByUid($touid)) {
			$array = array();
			foreach ($fresh as $key => $value) {
				$array[] = array(
					'uid' => $uid,
					'fresh_id' => $value['id'],
					'type' => $value['type'],
					'created_userid' => $value['created_userid'],
					'created_time' => $value['created_time']
				);
			}
			$this->_getFresh()->batchAddRelation($array);
		}

		PwSimpleHook::getInstance('addFollow')->runDo($uid, $touid);
		return true;
	}
	
	/**
	 * 用户(A)取消了对用户(B)关注
	 *
	 * @param int $uid 用户A
	 * @param int $touid 用户B
	 * @return bool| object PwError()
	 */
	public function deleteFollow($uid, $touid) {
		if (($result = $this->_getAttention()->deleteFollow($uid, $touid)) instanceof PwError) {
			return $result;
		}
		$this->_getTypeDs()->deleteUserType($uid, $touid);

		$user = $this->_getUser();
		$dm = new PwUserInfoDm($uid);
		$dm->addFollows(-1);
		$user->editUser($dm, PwUser::FETCH_DATA);

		$dm = new PwUserInfoDm($touid);
		$dm->addFans(-1);
		$user->editUser($dm, PwUser::FETCH_DATA);

		$this->_getFresh()->deleteAttentionFreshByUid($uid, $touid);

		PwSimpleHook::getInstance('deleteFollow')->runDo($uid, $touid);
		return true;
	}
 	
	public function addType($uid, $name) {
		$types = $this->getAllType($uid);
		if (count($types) > 20) {
			return new PwError('USER:attention.type.count.error');
		}
		if (in_array($name, $types)) {
			return new PwError('USER:attention.type.repeat');
		}
		return $this->_getTypeDs()->addType($uid, $name);
	}
	
 	/**
 	 * PwAttention
 	 *
 	 * @return PwAttention
 	 */
	protected function _getAttention() {
		return Wekit::load('attention.PwAttention');
	}

	protected function _getTypeDs() {
		return Wekit::load('attention.PwAttentionType');
	}

	protected function _getFresh() {
		return Wekit::load('attention.PwFresh');
	}
	
	/**
	 *
	 * @return PwAttentionRecommendFriendsService
	 */
	protected function _getRecommendService() {
		return Wekit::load('attention.srv.PwAttentionRecommendFriendsService');
	}
	
 	/**
 	 * PwUser
 	 *
 	 * @return PwUser
 	 */
	protected function _getUser() {
		return Wekit::load('user.PwUser');
	}
}
