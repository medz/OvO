<?php

/**
 * 可能认识的人  推荐关注
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwAttentionRecommendFriendsService {
	
	/**
	 * 更新某用户潜在好友
	 * 
	 * @param int $uid
	 */
	public function updateRecommendFriend($uid){
		$uid = intval($uid);
		if ($uid < 1) return false;
		$friends = $this->_getAttentionDs()->getFriendsByUid($uid);
		$fields = array();
		if ($friends) {
			$cnt = 1;
			$recommend_user = $sameUsers = $uids = array();
			foreach ($friends as $value) {
				$uids[] = $value['same_uid'];
				$uids[] = $value['recommend_uid'];
				$sameUsers[$value['recommend_uid']]['uid'] = $value['uid'];
				$sameUsers[$value['recommend_uid']]['recommend_uid'] = $value['recommend_uid'];
				$sameUsers[$value['recommend_uid']]['cnt'] += 1;
				$sameUsers[$value['recommend_uid']]['same_uid'][] = $value['same_uid'];
				$cnt++;
			}
			
			$attentions = $this->_getAttentionDs()->fetchFollows($uid, array_keys($sameUsers));
			$sameUsers = array_diff_key($sameUsers, $attentions);
			$userInfos = $this->_getUser()->fetchUserByUid(array_unique($uids));
			usort($sameUsers, array($this, 'orderByCnt'));
			// 更新用户data表信息
			$userData = array_slice($sameUsers, 0, 3);
			$this->updateUserData($uid, $userData, $userInfos);
			// 更新attention_recommend_friends表数据
			foreach ($sameUsers as $v) {
				$_temp['uid'] = $v['uid'];
				$_temp['recommend_uid'] = $v['recommend_uid'];
				$_temp['recommend_username'] = $userInfos[$v['recommend_uid']]['username'];
				$_temp['cnt'] = $v['cnt'];
				$_temp['recommend_user'] = implode(',', $v['same_uid']);
				$fields[] = $_temp;
			}
		}
		$this->_getRecommendFriendsDs()->deleteRecommendFriend($uid);
		$this->_getRecommendFriendsDs()->batchReplaceRecommendFriend($fields);
		return true;
	}
	
	/**
	 * 获取推荐用户缓存数据
	 * 
	 * @param int $uid
	 * @return array RecommentUsers
	 */
	public function getRecommentUser(PwUserBo $loginUser) {
		$recommends = $loginUser->info['recommend_friend'];
		if (!$recommends) return array();
		$recommends = explode('|', $recommends);
		$array = array();
		foreach ($recommends as $v) {
			if (!$v) continue;
			list($uid,$username,$cnt,$sameUser) = explode(',', $v);
			$array[$uid] = array(
				'uid' => $uid,
				'username'	=>	$username,
				'cnt'	=>	$cnt
			);
			$sameUser && $array[$uid]['sameUser'] = unserialize($sameUser);
		}
		return $array;
	}
	
	/**
	 * 获取推荐关注的用户
	 * 
	 * @param int $uid
	 * @param int $num
	 * @return array uids
	 */
	public function getPotentialAttention(PwUserBo $loginUser,$num) {
		$recomment = $this->getRecommentUser($loginUser);
		$recommentCount = count($recomment);
		if ($recommentCount >= $num) {
			return array_keys($recomment);
		}
		$num = $num - $recommentCount;
		$uids = $this->getRecommendAttention($loginUser->uid, $num);
		return array_unique(array_keys((array)$recomment) + $uids);
	}

	/**
	 * 根据规则获取推荐关注 | 
	 * 已取消
	 * 
	 * @param $uid 
	 * @param $num 
	 * @return array
	 */
	public function getRecommendAttention($uid,$num) {
		return $this->getOnlneUids($num);
	}
	
	/** 
	 * 组装关注用户数据
	 * 
	 * @param int $uid 用户uid
	 * @param array $recommendUids 推荐关注uids
	 * @param int $num
	 * @return array
	 */
	public function buildUserInfo($uid,$recommendUids,$num) {
		$attentions = $this->_getAttentionDs()->fetchFollows($uid,$recommendUids);
		$uids = array_diff($recommendUids,array($uid),array_keys($attentions));
		$uids = array_slice($uids, 0, $num);
		return $this->_getUser()->fetchUserByUid($uids);
	}
	
	public function getRecommendUsers($uid,$num) {
		$uids = $this->getRecommendAttention($uid,2*$num);
		return $this->buildUserInfo($uid, $uids, $num);
	}
	
	/** 
	 * 获取在线用户
	 * 
	 * @param int $num
	 * @return array uids
	 */
	public function getOnlneUids($num) {
		$onlineCount = $this->_getOnlineCountService()->getUserOnlineCount();
		if ($onlineCount > 0) {
			$start = $onlineCount > $num ? rand(0, $onlineCount - $num) : 0;
			$onlineUser = $this->_getUserOnlineDs()->getInfoList('',$start,$num);
			$onlineUids = array_keys($onlineUser);
		}
		return $onlineUids ? $onlineUids : array();
	}
	
	public function attentionUserRecommend($touid) {
		$loginUser = Wekit::getLoginUser();
		$this->_getRecommendDs()->deleteRecommendFriend($loginUser->uid, $touid);
		$this->_getRecommendFriendsDs()->deleteRecommendFriend($loginUser->uid, $touid);
		$recommend_user = $loginUser->info['recommend_friend'];
		$result = $this->_getRecommendFriendsDs()->getRecommendFriend($loginUser->uid, 3);
		$users = array();
		foreach ($result as $v) {
			$v['recommend_user'] && $users[] = unserialize($v['recommend_user']);
		}
		return $this->formatData($users);
	}
	
	public function updateUserData($uid, $users, $userInfos) {
		$user = '';
		if ($users) {
			$i = 0;
			foreach ($users as $u) {
				if (!isset($userInfos[$u['recommend_uid']])) continue;
				$user .= $u['recommend_uid'] . ',' . $userInfos[$u['recommend_uid']]['username'] . ',' . $u['cnt'];
				foreach ($u['same_uid'] as $value) {
					$same_uid[$value] = $userInfos[$value]['username'];
				}
				if ($i == 0 && $u['same_uid']) {

				}
				$user .= '|';
				$i++;
			}
			$user = rtrim($user,'|');
		}
		Wind::import('SRV:user.dm.PwUserInfoDm');
		$dm = new PwUserInfoDm($uid);
		$dm->setRecommendFriend($user);
		
		$this->_getUser()->editUser($dm, PwUser::FETCH_DATA);
		return true;
	}
	
	private function orderByCnt($a, $b) {
		    return strcmp($b['cnt'], $a['cnt']);
	}
	
	/**
	 * Enter description here ...
	 * 
	 * @return PwAttentionRecommendRecord
	 */
	private function _getRecommendDs(){
		return Wekit::load('attention.PwAttentionRecommendRecord');
	}
	
	/**
	 * Enter description here ...
	 * 
	 * @return PwAttentionRecommendFriends
	 */
	private function _getRecommendFriendsDs(){
		return Wekit::load('attention.PwAttentionRecommendFriends');
	}
	
	/**
	 * Enter description here ...
	 * 
	 * @return PwAttention
	 */
	private function _getAttentionDs(){
		return Wekit::load('attention.PwAttention');
	}
 	
 	/**
 	 * PwUserOnline
 	 *
 	 * @return PwUserOnline
 	 */
 	private function _getUserOnlineDs() {
 		return Wekit::load('online.PwUserOnline');
 	}
 	
 	/**
 	 * PwUser
 	 *
 	 * @return PwUser
 	 */
	protected function _getUser() {
		return Wekit::load('user.PwUser');
	}
	
 	/**
 	 * PwOnlineCountService
 	 *
 	 * @return PwOnlineCountService
 	 */
 	private function _getOnlineCountService() {
 		return Wekit::load('online.srv.PwOnlineCountService');
 	}
}