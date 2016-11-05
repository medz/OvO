<?php
Wind::import('ADMIN:library.AdminBaseController');
Wind::import('SRV:log.so.PwLogSo');

/**
 * 前台管理日志
 *
 * @author xiaoxia.xu<xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: ManageController.php 23742 2013-01-15 09:22:58Z jieyin $
 * @package src.applications.log.admin
 */
class ManageController extends AdminBaseController {
	protected $perpage = 10;
	
	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$page = intval($this->getInput('page'));
		($page < 1) && $page = 1;
		$logSo = new PwLogSo();
		$logSo->setEndTime($this->getInput('end_time'))
			->setStartTime($this->getInput('start_time'))
			->setCreatedUsername($this->getInput('created_user'))
			->setOperatedUsername($this->getInput('operated_user'))
			->setFid($this->getInput('fid'))
			->setIp($this->getInput('ip'))
			->setKeywords($this->getInput('keywords'))
			->setTypeid($this->getInput('typeid'));
		/* @var $logDs PwLog */
		$logDs = Wekit::load('log.PwLog');
		$count = $logDs->coutSearch($logSo);
		/* @var $logSrv PwLogService */
		$logSrv = Wekit::load('log.srv.PwLogService');
		$list = array();
		if ($count > 0) {
			($page > $count) && $page = $count;
			$totalPage = ceil($count / $this->perpage);
			list($offset, $limit) = Pw::page2limit($page, $this->perpage);
			$list = $logSrv->searchManageLogs($logSo, $limit, $offset);
		}
		$this->setOutput($logSrv->getOperatTypeid(), 'typeids');
		$this->setOutput($logSrv->getOperatTypeTitle(), 'typeTitles');
		$this->setOutput($this->perpage, 'perpage');
		$this->setOutput($list, 'list');
		$this->setOutput($count, 'count');
		$this->setOutput($page, 'page');
		$this->setOutput($logSo->getSearchData(), 'searchData');
		$this->_getForumList();
		$this->setOutput($this->isFounder($this->loginUser->username), 'canClear');
	}
	
	/**
	 * 清除三个月前操作
	 */
	public function clearAction() {
		if (!$this->isFounder($this->loginUser->username)) $this->showError('fail');
		$step = $this->getInput('step', 'post');
		if ($step != 2) $this->showError('fail');
		list($year, $month) = explode('-', Pw::time2str(Pw::getTime(), 'Y-n'));
		if ($month > 3) {
			$month = $month - 3;
		} else {
			$month = 9 - $month;
			$year = $year - 1;
		}
		Wekit::load('log.PwLog')->clearLogBeforeDatetime(Pw::str2time($year . '-' . $month . '-1'));
		$this->showMessage('success');
	}
	
	/**
	 * 获得版块列表
	 *
	 */
	private function _getForumList() {
		/* @var $forumSrv PwForumService */
		$forumSrv = Wekit::load('forum.srv.PwForumService');
		$map = $forumSrv->getForumMap();
		$catedb = $map[0];
		foreach ($catedb as $_k => $_v) {
			$catedb[$_k]['name'] = strip_tags($_v['name']);
		}
		$forumList = array();
		foreach ($catedb as $value) {
			$forumList[$value['fid']] = $this->_buildForumTree($value['fid'], $map);
		}
		$this->setOutput($catedb, 'catedb');
		$this->setOutput($forumList, 'forumList');
		$this->setOutput($forumSrv->getForumList(), 'allForumList');
	}
	
	/**
	 * 构建版块树
	 *
	 * @param int $parentid
	 * @param array $map
	 * @param int $level
	 * @return array
	 */
	private function _buildForumTree($parentid, $map, $level = '') {
		if (!isset($map[$parentid])) return array();
		$array = array();
		foreach ($map[$parentid] as $key => $value) {
			$value['level'] = $level;
			$value['name'] = strip_tags($value['name']);
			$array[] = $value;
			$array = array_merge($array, $this->_buildForumTree($value['fid'], $map, $level.'&nbsp;&nbsp;'));
		}
		return $array;
	}
}