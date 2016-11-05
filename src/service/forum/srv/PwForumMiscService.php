<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.dm.PwForumDm');
Wind::import('SRV:forum.bo.PwForumBo');

/**
 * 版块服务接口(不常用的业务逻辑)
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwForumMiscService.php 18802 2012-09-27 10:17:30Z jieyin $
 * @package src.service.user.srv
 */
class PwForumMiscService {
	
	/**
	 * 用户被禁言的时候同步删除该用户的版主权限
	 *
	 * @param string $manage  被禁言的用户名
	 * @return boolean
	 */
	public function updateDataByUser($manage) {
		$manage = trim($manage);
		$forums = $this->_getForum()->getForumOrderByType();
		foreach ($forums as $key => $value) {
			$manager = str_replace(',' . $manage . ',', ',', $value['manager']);
			$upmanager = str_replace(',' . $manage . ',', ',', $value['uppermanager']);
			if ($manager != $value['manager'] || $upmanager = $value['uppermanager']) {
				$dm = new PwForumDm($key);
				$dm->setUpperManager($upmanager)->setManager($manager);
				$this->_getForum()->updateForum($dm, PwForum::FETCH_MAIN);
			}
		}
		return true;
	}
	
	/**
	 * 纠正版块额外的数据(上级版块、是否含有子版等统计数据)
	 */
	public function correctData() {
		$manager = $fups = $fupnames = array(0 => '');
		$hassub = $subFids = $allManager = array();
		$forums = $this->_getForum()->getForumOrderByType();
		foreach ($forums as $key => $value) {
			if ($value['parentid']) $hassub[$value['parentid']] = 1;
			if ($value['hassub']) $subFids[] = $value['fid'];
			$uppermanager = $manager[$value['parentid']];
			$fup = $fups[$value['parentid']];
			$fupname = $fupnames[$value['parentid']];
			if ($uppermanager != $value['uppermanager'] || $fup != $value['fup'] || $fupname != $value['fupname']) {
				$dm = new PwForumDm($key);
				$dm->setUpperManager($uppermanager)->setFup($fup)->setFupname($fupname);
				$this->_getForum()->updateForum($dm, PwForum::FETCH_MAIN);
			}
			if ($value['manager'] = trim($value['manager'], ',')) {
				$allManager = array_merge($allManager, explode(',', $value['manager']));
				$uppermanager = rtrim($uppermanager, ',') . ',' . $value['manager'] . ',';
			}
			$manager[$key] = $uppermanager;
			$fups[$key] = $key . ($fup ? (',' . $fup) : '');
			$fupnames[$key] = strip_tags($value['name']) . ($fupname ? ("\t" . $fupname) : '');
		}
		$hassubFids = array_keys($hassub);
		if ($fids = array_diff($hassubFids, $subFids)) {
			$dm = new PwForumDm(true);
			$dm->setHassub(1);
			$this->_getForum()->batchUpdateForum($fids, $dm, PwForum::FETCH_MAIN);
		}
		if ($fids = array_diff($subFids, $hassubFids)) {
			$dm = new PwForumDm(true);
			$dm->setHassub(0);
			$this->_getForum()->batchUpdateForum($fids, $dm, PwForum::FETCH_MAIN);
		}
		Wekit::load('user.srv.PwUserMiscService')->updateManager($allManager);
	}
	
	/**
	 * 重新统计所有版块的帖子数
	 */
	public function countAllForumStatistics() {
		$forums = $this->_getForum()->getForumOrderByType(false);
		$fids = array_keys($forums);
		Wind::import('SRV:forum.dm.PwForumDm');
		$dm = new PwForumDm(true);
		$dm->setThreads(0)->setPosts(0)->setArticle(0)->setSubThreads(0);
		$this->_getForum()->batchUpdateForum($fids, $dm, PwForum::FETCH_STATISTICS);

		$threads = Wekit::load('forum.PwThreadExpand')->countThreadsByFid();
		$posts = Wekit::load('forum.PwThreadExpand')->countPostsByFid();
		foreach ($fids as $key => $value) {
			if (!isset($threads[$value]) && !isset($posts[$value])) continue;
			$dm = new PwForumDm($value);
			$dm->setThreads($threads[$value]['sum'])->setPosts($posts[$value]['sum']);
			$this->_getForum()->updateForum($dm, PwForum::FETCH_STATISTICS);
		}
		foreach ($fids as $key => $value) {
			$this->_getForum()->updateForumStatistics($value);
		}
	}

	public function updateForumCacheHook() {

	}

	public function updateForumCache() {

	}

	/**
	 * Enter description here ...
	 *
	 * @return PwForum
	 */
	protected function _getForum() {
		return Wekit::load('forum.PwForum');
	}
}