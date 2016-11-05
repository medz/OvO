<?php
Wind::import('SRV:invite.dm.PwInviteCodeDm');
Wind::import('SRV:credit.bo.PwCreditBo');
/**
 * 邀请码的服务类
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwInviteCodeService.php 18618 2012-09-24 09:31:00Z jieyin $
 * @package service.invite.srv
 */
class PwInviteCodeService {
	
	/**
	 * 检查该邀请码是否可以使用
	 *
	 * @param string $code
	 * @return PwError|true
	 */
	public function allowUseInviteCode($code) {
		$info = $this->_getDs()->getCode($code);
		if (!$info) return new PwError('USER:invite.code.error');
		if (1 == $info['ifused']) {
			return new PwError('USER:invite.code.isused');
		}
		$time = Wekit::C('register', 'invite.expired');
		$expireTime = Pw::getTime() - ($time * 86400);
		if ($info['created_time'] < $expireTime) return new PwError('USER:invite.code.expired');
		return $info;
	}
	
	/**
	 * 搜索邀请码列表
	 *
	 * @param PwInviteCodeSo $search 搜索的条件
	 * @param int $page 搜索的开始位置
	 * @param int $perpage
	 * @return array
	 */
	public function searchInvitecodeList(PwInviteCodeSo $search, $limit = 10, $start = 0) {
		$data = $this->_getDs()->searchCode($search, $limit, $start);
		if (!$data) return array();
		$result = array();
		$time = Wekit::C('register', 'invite.expired');
		$expire = Pw::getTime() - ($time * 86400);
		$_invitedUid = array();
		foreach ($data as $_item) {
			$_item['status'] = $_item['created_time'] < $expire ? '-1' : $_item['ifused'];
			$_item['expired_time'] = $_item['created_time'] + $time * 86400;
			$_item['invited_userid'] && $_invitedUid[] = $_item['invited_userid'];
			$result[] = $_item;
		}
		if ($_invitedUid) {
			/* @var $userDs PwUser */
			$userDs = Wekit::load('user.PwUser');
			$invitedUsers = $userDs->fetchUserByUid($_invitedUid);
			foreach ($result as $_k => $_item) {
				$result[$_k]['invited_username'] = $invitedUsers[$_item['invited_userid']] ? $invitedUsers[$_item['invited_userid']]['username'] : '';
			}
		}
		return $result;
	}
	
	/**
	 * 购买邀请码
	 *
	 * @param PwUserBo $user 购买的用户
	 * @param int $num  购买的数量
	 * @param int $creditType 用于购买的积分类型
	 * @return boolean
	 */
	public function buyInviteCodes(PwUserBo $user, $num, $creditType) {
		if (true !== ($r = $this->allowBuyInviteCode($user, $num, $creditType))) return $r;
		$num = intval(ceil($num));
		$codes = $this->createCodes(array(), $user->uid, $num);
		$data = array();
		$time = Pw::getTime();
		foreach ($codes as $_code) {
			$dm = new PwInviteCodeDm();
			$dm->setCode($_code)
				->setCreateUid($user->uid)
				->setCreatedTime($time);
			$data[] = $dm;
		}
		$r = $this->_getDs()->batchAddCode($data);
		if ($r instanceof PwError) return $r;
		$gidCreditNum = $user->getPermission('invite_buy_credit_num');
		
		//TODO【积分日志】购买邀请码
		/* @var $credit PwCreditBo */
		$credit = PwCreditBo::getInstance();
		$credit->sets($user->uid, array($creditType => -($gidCreditNum * $num)));
	}
	
	/**
	 * 判断用户是否可以购买邀请码
	 *
	 * @param PwUserBo $user 购买的用户
	 * @param int $num  购买的数量
	 * @param int $creditType 用于购买的积分类型
	 * @return boolean|PwError
	 */
	public function allowBuyInviteCode(PwUserBo $user, $num, $creditType) {
		if (!WindValidator::isPositive($num)) return new PwError('USER:invite.buy.num.error');
		$num = intval($num);
		
		//用户组能购买的邀请码数量限制
		$startTime = Pw::str2time(Pw::time2str(Pw::getTime(), 'Y-m-d'));
		$readyBuy = $this->_getDs()->countByUidAndTime($user->uid, $startTime);
		$gidLimit = abs(ceil($user->getPermission('invite_limit_24h')));
		if (($readyBuy + $num) > $gidLimit) {
			return new PwError('USER:invite.buy.num.24h.limit', array('{num}' => $gidLimit, '{readynum}' => $readyBuy));
		}
		
		$price = abs(ceil($user->getPermission('invite_buy_credit_num')));
		if (($price * $num) > $user->getCredit($creditType)) {
			return new PwError('USER:invite.buy.credit.no.enough', array('{num}' => $user->getCredit($creditType), '{buynum}' => $num));
		}
		return true;
	}
	
	/**
	 * 生成邀请码
	 *
	 * @param int $uid 用户ID
	 * @return string
	 */
	public function createInviteCode($uid) {
		$time = Pw::getTime();
		$string = WindUtility::generateRandStr(32 - strlen($uid) - strlen($time));
		return $uid . $string . $time;
	}

	/**
	 * 批量获得邀请码
	 *
	 * @param array 已经有的数据
	 * @param int $uid 购买的用户ID
	 * @param int $num 购买的数量
	 * @return 
	 */
	private function createCodes($data, $uid, $num) {
		$codes = array();
		for ($i = 0; $i < $num; $i ++) {
			$codes[] = $this->createInviteCode($uid);
		}
		$existCodes = $this->_getDs()->fetchCode($codes);
		$existCodes = array_keys($existCodes);
		if (!$existCodes) {
			return array_merge($data, $codes);
		}
		$data = array_merge($data, array_diff($codes, $existCodes));
		return $this->createCodes($data, $uid, $num - count($existCodes));
	}
	
	/**
	 * 获得邀请码的DS
	 *
	 * @return PwInviteCode
	 */
	private function _getDs() {
		return Wekit::load('invite.PwInviteCode');
	}
}