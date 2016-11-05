<?php
Wind::import('SRV:space.bo.PwSpaceBo');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwSpaceModel.php 20893 2012-11-16 07:00:39Z jieyin $ 
 * @package 
 */
 
class PwSpaceModel extends PwSpaceBo {
 	
 	/**
	 * 模块调用接口
	 * Enter description here ...
	 * @param string $mod visit|tovisit|tag|keys|fllow|fans
	 * @param int $limit
	 * @param int $page
	 */
	public function model($mod, $limit = 10, $page = 0) {
		$limit = (int)$limit;
		$page = (int)$page;
		if ($page > 0 ) {
			$method = sprintf('model%sList', ucwords($mod));
			if (!method_exists($this, $method)) return array();
			return $this->$method($limit, $page);
		} else {
			$method =  sprintf('model%s', ucwords($mod));
			if (!method_exists($this, $method)) return array();
			return $this->$method($limit);
		}
	}
 	
	protected function modelVisit($limit) {
		$_users = array();
		$visitors = unserialize($this->space['visitors']);
		if (!$visitors) return array(0,array());
		$count = count($visitors);
		$visitors = is_array($visitors) ? array_slice($visitors, 0, $limit, true) : array();
		$uids = array_keys($visitors);
		$users = Wekit::load('user.PwUser')->fetchUserByUid($uids);
		foreach ($visitors AS $k=>$v) {
			$users[$k]['visitor_time'] = $v;
			$_users[$k] = $users[$k];
		}
	
		return array($count, $_users);
	}
	
	protected function modelTovisit($limit) {
		$_users = array();
		$tovisitors = unserialize($this->space['tovisitors']);
		if (!$tovisitors) return array(0,array());
		$count = count($tovisitors);
		$tovisitors =  is_array($tovisitors) ? array_slice($tovisitors, 0, $limit, true) : array();
		$uids = array_keys($tovisitors);
		$users = Wekit::load('user.PwUser')->fetchUserByUid($uids);
		foreach ($tovisitors AS $k=>$v) {
			$users[$k]['visitor_time'] = $v;
			$_users[$k] = $users[$k];
		}
		return array($count, $_users);
	}
	
	protected function modelTag($limit) {
		$count = Wekit::load('tag.PwTagAttention')->countAttentionByUid($this->spaceUid);
 		return array($count, Wekit::load('tag.PwTag')->getAttentionByUid($this->spaceUid, 0, $limit));
	}
	
	protected function modelUserTag($limit) {
		$tags = Wekit::load('usertag.srv.PwUserTagService')->getUserTagList($this->spaceUid);
		$count = count($tags);
		$tags =  is_array($tags) ? array_slice($tags, 0, $limit, true) : array();
 		return array($count, $tags);
	}
	
 	protected function modelFollow($limit) {
 		$count = $this->spaceUser['follows'];
 		$follows = Wekit::load('attention.PwAttention')->getFollows($this->spaceUid, $limit, 0);
 		$uids = array_keys($follows);
 		return array($count, Wekit::load('user.PwUser')->fetchUserByUid($uids));
	}
	
 	protected function modelFans($limit) {
 		$count = $this->spaceUser['fans'];
 		$fans = Wekit::load('attention.PwAttention')->getFans($this->spaceUid, $limit, 0);
 		$uids = array_keys($fans);
 		return array($count, Wekit::load('user.PwUser')->fetchUserByUid($uids));
	}
	
 	protected function modelThreadList($limit, $page) {
 		$ds = Wekit::load('forum.PwThread');
 		$count =$ds->countThreadByUid($this->spaceUid);
		if ($count) {
			list($offset,$limit) = Pw::page2limit($page, $limit);
			$threads = $ds->getThreadByUid($this->spaceUid,$limit,$offset, 2);
		}
	    return array($count, $threads);
	}
	
 	protected function modelPostList($limit, $page) {
 		$ds = Wekit::load('forum.PwThread');
		list($offset,$limit) = Pw::page2limit($page, $limit);
 		$count = $ds->countPostByUid($this->spaceUid);
		if ($count) {
			$tmpPosts = $ds->getPostByUid($this->spaceUid, $limit, $offset);
			$posts = $tids = array();
			foreach ($tmpPosts as $v) {
				$tids[] = $v['tid'];
			}
			$threads = $this->_getThreadDs()->fetchThread($tids);
			foreach ($tmpPosts as $v) {
				$v['threadSubject'] = Pw::substrs($threads[$v['tid']]['subject'], 30);
				$v['content'] = Pw::substrs($v['content'], 30);
				$v['created_time'] = PW::time2str($v['created_time'],'auto');
				$posts[] = $v;
			}
		}
		 return array($count, $posts);
	}
	
 	protected function modelFreshList($limit, $page) {
 		Wind::import('SRV:attention.srv.PwFreshDisplay');
		Wind::import('SRV:attention.srv.dataSource.PwFetchMyFresh');
		$count = Wekit::load('attention.PwFresh')->countFreshByUid($this->spaceUid);
		$totalpage = ceil($count / $limit);
		$page > $totalpage && $page = $totalpage;
 		list($offset,$limit) = Pw::page2limit($page, $limit);
 		$freshDisplay = new PwFreshDisplay(new PwFetchMyFresh($this->spaceUid, $limit, $offset));
		return array($count, $freshDisplay->gather());
	}
 }
?>