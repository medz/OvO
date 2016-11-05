<?php
/**
 * @author Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwLikeService.php 20027 2012-10-22 11:49:23Z gao.wanggao $ 
 * @package 
 */
 class PwLikeService {
 	
 	/**
 	 * 喜欢增加策略
 	 * Enter description here ...
 	 * @param PwUserBo $userBo
 	 * @param int $typeid
 	 * @param int $fromid
 	 */
 	public function addLike(PwUserBo $userBo, $typeid, $fromid = 0) {
 		$uid = $userBo->uid;
		$likeDs = $this->_getLikeContentDs();
		list($beLikeUid, $isspecial, $count, $fid, $extend) = $this->_getSourceInfo($typeid, $fromid);
		if ($beLikeUid == $uid) return new PwError('BBS:like.fail.myself.post');
		
		$time = Pw::getTime();
		
		//判断是否存在喜欢内容
		$info = $likeDs->getInfoByTypeidFromid($typeid, $fromid);
		$likeid = isset($info['likeid']) ?  (int)$info['likeid'] : 0;
		Wind::import('SRV:like.dm.PwLikeDm');
		$dm = new PwLikeDm();
		$dm->setTypeid($typeid)
			->setFromid($fromid)
			->setIsspecial($isspecial);
		if ($likeid < 1) $likeid = $likeDs->addInfo($dm);
		
		//判断是否喜欢过
		$logInfo = $this->_getLikeLogDs()->getInfoByUidLikeid($uid, $likeid);
		if ($logInfo) return new PwError('BBS:like.fail.already.liked');
		
		//写入喜欢记录
		Wind::import('SRV:like.dm.PwLikeLogDm');
		$logDm = new PwLikeLogDm();
		$logDm->setUid($uid)
			->setLikeid($likeid)
			->setCreatedTime($time);
		$logid = $this->_getLikeLogDs()->addInfo($logDm);
		if ($logid < 1 ) return new PwError('BBS:like.fail');
		
		//更新喜欢内容
		$count ++; 
		$this->_updateLikeCount($typeid, $fromid, $count);
		$likeDs->updateUsers($likeid, $uid);
		
		//写入用户喜欢统计
		Wind::import('SRV:user.dm.PwUserInfoDm');
		$likeNumber = isset($userBo->info['likes']) ? intval($userBo->info['likes']) : 0;
		$likeNumber++;
 		$infoDm = new PwUserInfoDm($uid);
 		$infoDm->setLikes($likeNumber);
		$userDs = Wekit::load('user.PwUser');
 		$userDs->editUser($infoDm, PwUser::FETCH_DATA);
 		
 		//用户积分
 		Wind::import('SRV:forum.bo.PwForumBo');
 		$forumBo = new PwForumBo($fid);
 		Wind::import('SRV:credit.bo.PwCreditBo');
		$credit = PwCreditBo::getInstance();
		$credit->operate('belike', new PwUserBo($beLikeUid), true, array('forumname' =>$userBo->username), $forumBo->getCreditSet('belike'));
		$credit->execute();
 		
 		//喜欢挂勾
 		$dm->setBeLikeUid($beLikeUid);
		//$this->_getHook()->runDo('addLike', $userBo, $dm);
		PwSimpleHook::getInstance('PwLikeService_addLike')->runDo($userBo, $dm);
		
		//喜欢后续操作 如果不需要排行，return true
		$this->setLikeBrand($likeid, $count, $typeid, $fromid);
		return array('likeCount'=>$likeNumber, 'extend'=>$extend);
	}

 	
 	public function delLike($uid, $logid) {
 		$info = $this->allowEditLike($uid, $logid);
		if ($info instanceof PwError) return false;
		if (!$this->_getLikeLogDs()->deleteInfo($logid)) return false;
		$likeInfo = $this->_getLikeContentDs()->getLikeContent($info['likeid']);
		if (!$likeInfo) return false;
		list($beLikeUid, $isspecial, $count, $fid) = $this->_getSourceInfo($likeInfo['typeid'], $likeInfo['fromid']);
		$count--;
		$this->_updateLikeCount($likeInfo['typeid'], $likeInfo['fromid'], $count);
		
		
		//删除喜欢tag
		if ($info['tagids']) {
			$this->_getLikeRelationsDs()->deleteInfosBylogid($logid);
			$tagids = explode(',', $info['tagids']);
			foreach ($tagids AS $tagid) {
				$this->_getLikeTagDs()->updateNumber($tagid, false);
			}
		}
		//写入喜欢统计
		Wind::import('SRV:user.dm.PwUserInfoDm');
		$userDs = Wekit::load('user.PwUser');
		$userStatistics = $userDs->getUserByUid($uid, PwUser::FETCH_DATA);
		$likeNumber = isset($userStatistics['likes']) ? intval($userStatistics['likes']) : 0;
		$likeNumber--;
 		$dm = new PwUserInfoDm($uid);
 		$dm->setLikes($likeNumber);
 		Wekit::load('user.PwUser')->editUser($dm, PwUser::FETCH_DATA);
 	
 		//喜欢后续操作
 		//$this->_getHook()->runDo('delLike', $uid, $beLikeUid);
 		PwSimpleHook::getInstance('PwLikeService_delLike')->runDo($uid, $beLikeUid);
		return true;
 	}
 	
 	/**
 	 * 标签更新策略  不再使用
 	 * 
 	 * @param int $uid
 	 * @param array $tagids
 	 * @param int $logid
 	 * @param array $tags
 	 */
 	public function addLikeTag($uid, $tagids, $logid, $tags) {
 		if (!$tags) return new PwError('BBS:like.tagname.not.empty');
 		$tagnames = array_filter(explode(' ', $tags));
 		$tagnames = array_unique($tagnames);
 		foreach ($tagnames AS $k=>$tag) {
 			$tag = trim($tag);
 			if (Pw::strlen($tag) < 2 || Pw::strlen($tag) > 10) unset($tagnames[$k]);
 		}
		$_tagids = empty($tagids)? array() : explode(',', $tagids);
		if (count($tagnames) > 5) $tagnames = array_slice($tagnames, 0, 5);
		$newTags = $this->diffTagNames($tagnames, $uid);
 		//写入新的Tag
		if ($newTags) {
			Wind::import('SRV:like.dm.PwLikeTagDm');
			foreach ($newTags AS $newTag) {
				$dm = new PwLikeTagDm();
				$dm->setTagname($newTag)
					->setUid($uid)
					->setNumber(0);
				$tagid = $this->_getLikeTagDs()->addInfo($dm);
				$_tagids[] = $tagid;
			}
		}
		$logInfo = $this->_getLikeLogDs()->getLikeLog($logid);
		
		//更新log Tag
		Wind::import('SRV:like.dm.PwLikeLogDm');
		$logDm = new PwLikeLogDm($logid);
		$logDm->setTagids($_tagids);
		$this->_getLikeLogDs()->updateInfo($logDm);	
		
		//删除关系表
		$this->_getLikeRelationsDs()->deleteInfosBylogid($logid);

		//增加新的关系表
		foreach ($_tagids AS $tagid) {
			$this->_getLikeRelationsDs()->addInfo($logid, $tagid);
		}
		//对原tag计数减1
		$_logTagids = empty($logInfo['tagids'])? array() : explode(',', $logInfo['tagids']);
		foreach ( $_logTagids AS $tagid) {
			$this->_getLikeTagDs()->updateNumber($tagid, false);
		}
		//对所有tag计数加1
		foreach ($_tagids AS $tagid) {
			$this->_getLikeTagDs()->updateNumber($tagid);
		}
		//返回修改后的tag列表
		return $this->_getLikeTagDs()->fetchLikeTag($_tagids);
 	}
 	
 	/**
 	 * 新增分类
 	 * Enter description here ...
 	 * @param int $logid
 	 * @param string $tagname
 	 */
 	public function addTag($uid, $tagname) {
 		if (Pw::strlen($tagname) < 2) return new PwError('BBS:like.tagname.is.short');
 		if (Pw::strlen($tagname) > 10) return new PwError('BBS:like.tagname.is.lenth');
 		$tagInfos = $this->_getLikeTagDs()->getInfoByUid($uid);
		foreach ($tagInfos AS $info) {
			if ($tagname == $info['tagname']) return new PwError('BBS:like.tagname.is.already');
		}
 		Wind::import('SRV:like.dm.PwLikeTagDm');
		$dm = new PwLikeTagDm();
		$dm->setTagname($tagname)
			->setUid($uid)
			->setNumber(0);
		return $this->_getLikeTagDs()->addInfo($dm);
 	}
 	
 	
 	/**
 	 * 对喜欢所属分类进行增减
 	 * @param int $type  1 增加， 0 减
 	 */
 	public function editLogTag($logid, $tagid, $type = 0) {
 		$logInfo = $this->_getLikeLogDs()->getLikeLog($logid);
 		$tagids =  explode(',', $logInfo['tagids']);
 		if ($type) {
 			$tagids[] = $tagid;
 			$this->_getLikeRelationsDs()->addInfo($logid, $tagid);
 			$this->_getLikeTagDs()->updateNumber($tagid);
 		} else {
 			$k = array_search($tagid,$tagids);
 			if ($k) unset($tagids[$k]);
 			$this->_getLikeRelationsDs()->deleteInfo($logid, $tagid);
 			$this->_getLikeTagDs()->updateNumber($tagid, false);
 		}
 		
 		//更新log Tag
		Wind::import('SRV:like.dm.PwLikeLogDm');
		$logDm = new PwLikeLogDm($logid);
		$logDm->setTagids($tagids);
		$this->_getLikeLogDs()->updateInfo($logDm);	
		return true;
 	}
 	
 	/**
 	 * 获取喜欢榜单
 	 * 
 	 * @param string $key
 	 * @param int $start
 	 * @param int $limit
 	 * @param bool $isthread
 	 */
	public function getLikeBrand($key, $start = 0, $limit = 10, $isthread = false) {
		$statis = $this->_getLikeStatisticsDs()->getInfoList($key,  $start, $limit, $isthread);
		$likeids = $tids = array();
		if (empty($statis)) return array();
		$likeds = $this->_getLikeContentDs();
		foreach ($statis AS $val) {
			if ($val['typeid'] != PwLikeContent::THREAD) continue;
			$tids[] = $val['fromid'];
			$likeids[] = $val['likeid'];
		}
		
		$threads = $this->_getThreadDs()->fetchThread($tids);
		$likes = $likeds->fetchLikeContent($likeids);
		foreach ($likes AS $key=>$val) {
			if (!$threads[$val['fromid']]['subject']) {
				unset($likes[$key]);
			} else {
				$likes[$key]['subject'] = $threads[$val['fromid']]['subject'];
			}
		}
		return  $likes;
	}
	
	/**
	 * 新标签过滤
	 * 
	 * @param string $tagnames
	 * @param array $tagids
	 */
 	public function diffTagNames($tagnames, $uid) {
		$_tagnames = array();
		
		if (!is_array($tagnames) || count($tagnames) <1 ) return false;
		$tagInfos = $this->_getLikeTagDs()->getInfoByUid($uid);
		foreach ($tagInfos AS $info) {
			$_tagnames[] = $info['tagname'];
		}
		return array_diff($tagnames, $_tagnames);
	}
	
 	/**
	 * 喜欢增加策略后续操作：更新喜欢排行榜
	 * 
	 * $signKeys 排行榜时间，按相对时间排行
	 * $countKeys 每种排行的当前记录数
	 * $maxStatis 最大记录数
	 * @param int $likeid
	 * @param int $count
	 */
	public  function setLikeBrand($likeid, $count, $typeid, $fromid) {
		$signKeys = array('day7'=>604800, 'day2'=>172800, 'day1'=>86400);
		$countKeys = array('day7_count', 'day2_count', 'day1_count');
		$minInfo = $this->_getLikeStatisticsDs()->getMinInfo('day7');
		$minCount = $minInfo ? $minInfo['number'] : 0 ;
		$maxStatis = 100;
		$time = Pw::getTime();
		if ($minCount > $count) return false;
		foreach ($signKeys AS $key => $value) {
			$startTime = $time - $value;
			$keyInfo = $this->_getLikeStatisticsDs()->getLikeStatistics($key.'_count');
			$keyCount = $keyInfo ? $keyInfo['number'] : 0 ;
			if ($minCount < $count || $keyCount < $maxStatis) {
				$logCount = $this->_getLikeLogDs()->getLikeidCount($likeid, $startTime);
				Wind::import('SRV:like.dm.PwLikeStatisticsDm');
				$dm = new PwLikeStatisticsDm();
				$dm->setSignkey($key)
					->setLikeid($likeid)
					->setTypeid($typeid)
					->setFromid($fromid)
					->setNumber($logCount);
				$msg = $this->_getLikeStatisticsDs()->addInfo($dm);
				if (is_numeric($msg) && $keyCount < $maxStatis) {
					$dm = new PwLikeStatisticsDm();
					$keyCount++;
					$dm->setSignkey($key.'_count')
						->setLikeid(0)
						->setNumber($keyCount);
					$this->_getLikeStatisticsDs()->addInfo($dm);
				}
			}
		}
		return true;
	}
	
	/**
	 * 判断喜欢编辑部权限
	 * 
	 * @param $logid
	 */
 	public function allowEditLike($uid, $logid) {
		if($logid < 1) return new PwError('BBS:like.fail');
		$info = $this->_getLikeLogDs()->getLikeLog($logid);
		if ($info['uid'] < 1 || $info['uid'] != $uid) return new PwError('BBS:like.fail');
		return $info;

	}
	
	public function allowEditTag($uid, $tagid) {
		if ($tagid < 1 ) return new PwError('BBS:like.fail');
		$info = $this->_getLikeTagDs()->getLikeTag($tagid);
		if (!$info || $info['uid'] < 1 )return new PwError('BBS:like.tagname.empty');
		if ( $info['uid'] != $uid) return new PwError('BBS:like.permissions.fail');
		return $info;
	}
	
 	
	private function _getSourceInfo($typeid, $fromid) {
		$extend = array();
		switch ($typeid) {
			case PwLikeContent::THREAD:
				$msg = Wekit::load('forum.PwThread')->getThread($fromid);
				
				//needcheck
				Wind::import('SRV:forum.srv.post.PwReplyPost');
				Wind::import('SRV:forum.srv.PwPost');
				$postAction = new PwReplyPost($fromid);
				$post = new PwPost($postAction);
				if ($post->getDisabled()){
					$extend = array('needcheck'=>true);
				}
				
 				return array($msg['created_userid'], $msg['special'], $msg['like_count'], $msg['fid'], $extend); 
			case PwLikeContent::POST:
				$msg = Wekit::load('forum.PwThread')->getPost($fromid);
 				return array($msg['created_userid'], 0, $msg['like_count'], $msg['fid']); 
 			case PwLikeContent::WEIBO:
				$msg = Wekit::load('weibo.PwWeibo')->getWeibo($fromid);
 				return array($msg['created_userid'], 0, $msg['like_count'], 0); 
 			case PwLikeContent::APP:
 				$msg = Wekit::load('like.PwLikeSource')->getSource($fromid);
 				return array(0, 0, $msg['like_count'], 0); 
		}
	}
	
	private function _updateLikeCount($typeid, $fromid, $count) {
		switch ($typeid) {
			case PwLikeContent::THREAD:
				Wind::import('SRV:forum.dm.PwTopicDm');
				$dm = new PwTopicDm($fromid);
				$dm->setLikeCount($count);
				return Wekit::load('forum.PwThread')->updateThread($dm, PwThread::FETCH_MAIN);
			case PwLikeContent::POST:
				Wind::import('SRV:forum.dm.PwReplyDm');
				$dm = new PwReplyDm($fromid);
				$dm->setLikeCount($count);
				return Wekit::load('forum.PwThread')->updatePost($dm);
 			case PwLikeContent::WEIBO:
				Wind::import('SRV:weibo.dm.PwWeiboDm');
				$dm = new PwWeiboDm($fromid);
				$dm->setLikeCount($count);
				return Wekit::load('weibo.PwWeibo')->updateWeibo($dm);
 			case PwLikeContent::APP:
 				Wind::import('SRV:like.dm.PwLikeSourceDm');
				$dm = new PwLikeSourceDm($fromid);
				$dm->setLikeCount($count);
				return Wekit::load('like.PwLikeSource')->updateSource($dm);
		}
	}
	
 	private function _getThreadDs() {
		return Wekit::load('forum.PwThread');
	}
	
 	private function _getLikeTagDs() {
		return Wekit::load('like.PwLikeTag');
	}
	
 	private function _getLikeLogDs() {
		return Wekit::load('like.PwLikeLog');
	}
	
 	private function _getLikeContentDs() {
		return Wekit::load('like.PwLikeContent');
	}
	
 	private function _getLikeStatisticsDs() {
		return Wekit::load('like.PwLikeStatistics');
	}
	
 	private function _getLikeRelationsDs() {
		return Wekit::load('like.PwLikeRelations');
	}
	/*
 	private function _getHook() {
 		return new PwHookService('PwLikeService', 'PwLikeDoBase');
 	}*/
 }
?>