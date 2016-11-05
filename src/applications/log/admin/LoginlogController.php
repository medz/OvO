<?php
Wind::import('ADMIN:library.AdminBaseController');
Wind::import('SRV:log.so.PwLogSo');

/**
 * 前台管理日志
 *
 * @author xiaoxia.xu<xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: LoginlogController.php 25604 2013-03-20 01:24:06Z gao.wanggao $
 * @package src.applications.log.admin
 */
class LoginlogController extends AdminBaseController {
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
			->setTypeid($this->getInput('typeid'))
			->setIp($this->getInput('ip'));
		/* @var $logDs PwLogLogin */
		$logDs = Wekit::load('log.PwLogLogin');
		$count = $logDs->coutSearch($logSo);
		$list = array();
		if ($count > 0) {
			($page > $count) && $page = $count;
			$totalPage = ceil($count / $this->perpage);
			list($offset, $limit) = Pw::page2limit($page, $this->perpage);
			$list = $logDs->search($logSo, $limit, $offset);
		}
		$this->setOutput($this->perpage, 'perpage');
		$this->setOutput($list, 'list');
		$this->setOutput($count, 'count');
		$this->setOutput($page, 'page');
		$this->setOutput($logSo->getSearchData(), 'searchData');
		$this->setOutput($this->isFounder($this->loginUser->username), 'canClear');
		$this->setOutput($this->_getLoginType(), 'types');
		$this->setTemplate('manage_login');
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
		Wekit::load('log.PwLogLogin')->clearLogBeforeDatetime(Pw::str2time($year . '-' . $month . '-1'));
		$this->showMessage('success');
	}
	
	/**
	 * 返回登录的错误类型
	 *
	 * @return array
	 */
	private function _getLoginType() {
		return array(PwLogLogin::ERROR_PWD => '密码错误', PwLogLogin::ERROR_SAFEQ => '安全问题错误');
	}
}