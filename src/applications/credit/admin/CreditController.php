<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('ADMIN:library.AdminBaseController');
Wind::import('SRV:credit.srv.PwCreditOperationConfig');
Wind::import('SRV:credit.bo.PwCreditBo');

/**
 * 积分设置
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: CreditController.php 4132 2012-02-11 05:35:07Z xiaoxia.xuxx $
 * @package src.products.admin.controller
 */
class CreditController extends AdminBaseController {

	/**
	 * 积分设置-展示页面
	 *
	 * @see WindController::run()
	 */
	public function run() {
		$this->setCurrentTab('run');
		$credits = $this->_getCreditService()->getCredit();
		ksort($credits);

		$creditConfig = Wekit::C()->getValues('credit');
		$this->setOutput($credits, 'credits');
		$this->setOutput($creditConfig['credits'] ? $creditConfig['credits'] : array(), 'localCredits');
	}

	/**
	 * 积分设置-保存设置操作
	 */
	public function doSettingAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		$credits = $this->getInput('credits', 'post');
		if (!is_array($credits) || empty($credits)) {
			$this->showError("CREDIT:setting.dataError", "credit/credit/run");
		}
		$this->_getCreditService()->setCredits($credits, $this->getInput('newcredits', 'post'));
		$this->showMessage("CREDIT:setting.success", "credit/credit/run");
	}

	/**
	 * 删除积分操作
	 */
	public function doDeleteAction() {
		$creditId = (int) $this->getInput("creditId", 'post');
		if (!$creditId) {
			$this->showError('operate.fail');
		}

		if ($creditId < 5) $this->showError('CREDIT:setting.doDelete.fail', 'credit/credit/run');
		if (($result = $this->_getCreditService()->deleteCredit($creditId)) instanceof PwError) {
			$this->showError($result->getError(), "credit/credit/run");
		}
		$this->showMessage("CREDIT:setting.doDelete.success", "credit/credit/run");
	}

	/**
	 * 积分策略-展示策略页面
	 */
	public function strategyAction() {
		$this->setCurrentTab('strategy');
		// 所有的模块
		/* @var $config PwCreditOperationConfig */
		$config = PwCreditOperationConfig::getInstance();
		$creditConfig = Wekit::C()->getValues('credit');
		
		$this->setOutput($config->getMap(), 'allModules');
		$this->setOutput($config->getData(), 'moduleConfig');
		$this->setOutput(PwCreditBo::getInstance(), 'creditBo');
		$this->setOutput($creditConfig['strategy'] ? $creditConfig['strategy'] : array(), 'strategy');
	}

	/**
	 * 积分策略-编辑策略操作
	 */
	public function editStrategyAction() {
		$info = $this->getInput('info');
		
		$creditConfig = Wekit::C()->getValues('credit');
		$strategy = $creditConfig['strategy'] ? $creditConfig['strategy'] : array();
		if (is_array($info)) {
			foreach ($info as $key => $value) {
				!is_numeric($value['limit']) && $info[$key]['limit'] = '';
				foreach ($value['credit'] as $k => $v) {
					!is_numeric($v) && $info[$key]['credit'][$k] = '';
				}
			}
			$strategy = array_merge($strategy, $info);
		}
		
		$config = new PwConfigSet('credit');
		$config->set('strategy', $strategy)->flush();
		$this->showMessage('CREDIT:strategy.update.success', 'credit/credit/strategy');
	}

	/**
	 * 积分充值-展示页面
	 */
	public function rechargeAction() {
		Wind::import('SRV:credit.bo.PwCreditBo');
		
		$this->setOutput(PwCreditBo::getInstance(), 'creditBo');
		$this->setOutput(Wekit::C('credit', 'recharge'), 'recharge');
		$this->setCurrentTab('recharge');
	}

	/**
	 * 积分充值-充值设置操作
	 */
	public function dorechargeAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		list($recharge, $ctype, $rate, $min) = $this->getInput(
			array('recharge', 'ctype', 'rate', 'min'));
		
		is_array($recharge) || $recharge = array();
		is_array($ctype) || $ctype = array();
		foreach ($ctype as $key => $value) {
			if ($rate[$key] && !isset($recharge[$value])) {
				$recharge[$value] = array(
					'rate' => intval($rate[$key]), 
					'min' => $min[$key] ? $min[$key] : '');
			}
		}
		$config = new PwConfigSet('credit');
		$config->set('recharge', $recharge)->flush();
		
		$this->showMessage('operate.success');
	}

	/**
	 * 积分转换-展示页面
	 */
	public function exchangeAction() {
		Wind::import('SRV:credit.bo.PwCreditBo');
		
		// print_r(Wekit::C('credit', 'exchange'));
		$this->setOutput(PwCreditBo::getInstance(), 'creditBo');
		$this->setOutput(Wekit::C('credit', 'exchange'), 'exchange');
		$this->setCurrentTab('exchange');
	}

	/**
	 * 积分转换-编辑操作
	 */
	public function doexchangeAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		list($exchange_old, $ifopen_old, $credit1, $credit2, $value1, $value2, $ifopen) = $this->getInput(
			array('exchange_old', 'ifopen_old', 'credit1', 'credit2', 'value1', 'value2', 'ifopen'));
		$old = array();
		$exchange = Wekit::C('credit', 'exchange');
		foreach ($exchange as $key => $value) {
			if (isset($exchange_old[$key])) {
				$exchange[$key]['ifopen'] = $ifopen_old[$key] ? 1 : 0;
			} else {
				unset($exchange[$key]);
			}
		}
		
		is_array($credit1) || $credit1 = array();
		foreach ($credit1 as $key => $value) {
			if (!$value || !$credit2[$key] || !$value1[$key] || !$value2[$key]) continue;
			if ($value == $credit2[$key]) {
				$this->showError('CREDIT:exchange.fail.credit.same');
			}
			$vkey = $value . '_' . $credit2[$key];
			$exchange[$vkey] = array(
				'credit1' => $value, 
				'credit2' => $credit2[$key], 
				'value1' => $value1[$key], 
				'value2' => $value2[$key], 
				'ifopen' => $ifopen[$key] ? 1 : 0);
		}
		$config = new PwConfigSet('credit');
		$config->set('exchange', $exchange)->flush();
		
		$this->showMessage('operate.success');
	}

	public function delexchangeAction() {
		$id = $this->getInput('id', 'post');
		if (!$id) {
			$this->showError('operate.fail');
		}

		$exchange = Wekit::C('credit', 'exchange');
		if (isset($exchange[$id])) {
			unset($exchange[$id]);
			$config = new PwConfigSet('credit');
			$config->set('exchange', $exchange)->flush();
		}
		$this->showMessage('operate.success');
	}

	/**
	 * 积分转账设置页面
	 */
	public function transferAction() {
		Wind::import('SRV:credit.bo.PwCreditBo');
		$transfer = Wekit::C('credit', 'transfer');
		$this->setOutput(PwCreditBo::getInstance(), 'creditBo');
		$this->setOutput($transfer ? $transfer : array(), 'transfer');
		$this->setCurrentTab('transfer');
	}

	/**
	 * 积分转账设置操作
	 */
	public function dotransferAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		list($ifopen, $rate, $min) = $this->getInput(array('ifopen', 'rate', 'min'));
		
		Wind::import('SRV:credit.bo.PwCreditBo');
		$creditBo = PwCreditBo::getInstance();
		$transfer = array();
		
		foreach ($creditBo->cType as $key => $value) {
			if (!$ifopen[$key] && !$rate[$key] && !$min[$key]) continue;
			$transfer[$key] = array(
				'ifopen' => $ifopen[$key] ? 1 : 0, 
				'rate' => $rate[$key] ? intval($rate[$key]) : '', 
				'min' => $min[$key] ? intval($min[$key]) : '');
		}
		$config = new PwConfigSet('credit');
		$config->set('transfer', $transfer)->flush();
		
		$this->showMessage('operate.success');
	}

	/**
	 * 积分日志页面
	 */
	public function logAction() {
		list($ctype, $time_start, $time_end, $award, $username, $uid) = $this->getInput(
			array('ctype', 'time_start', 'time_end', 'award', 'username', 'uid'));
		
		$page = $this->getInput('page');
		$page < 1 && $page = 1;
		$perpage = 20;
		list($offset, $limit) = Pw::page2limit($page, $perpage);
		
		Wind::import('SRV:credit.bo.PwCreditBo');
		Wind::import('SRV:credit.vo.PwCreditLogSc');
		Wind::import('SRV:credit.srv.PwCreditOperationConfig');
		
		$sc = new PwCreditLogSc();
		$url = array();
		if ($ctype) {
			$sc->setCtype($ctype);
			$url['ctype'] = $ctype;
		}
		if ($time_start) {
			$sc->setCreateTimeStart(Pw::str2time($time_start));
			$url['time_start'] = $time_start;
		}
		if ($time_end) {
			$sc->setCreateTimeEnd(Pw::str2time($time_end));
			$url['time_end'] = $time_end;
		}
		if ($award) {
			$sc->setAward($award);
			$url['award'] = $award;
		}
		if ($username) {
			$user = Wekit::load('user.PwUser')->getUserByName($username);
			$sc->setUserid($user['uid']);
// 			$url['uid'] = $user['uid'];
			$url['username'] = $username;
		}
		if ($uid) {
			$sc->setUserid($uid);
			$url['uid'] = $uid;
		}
		$count = Wekit::load('credit.PwCreditLog')->countBySearch($sc);
		$log = Wekit::load('credit.PwCreditLog')->searchLog($sc, $limit, $offset);
		
		$this->setCurrentTab('log');
		$this->setOutput(PwCreditBo::getInstance(), 'creditBo');
		$this->setOutput(PwCreditOperationConfig::getInstance(), 'coc');
		$this->setOutput($log, 'log');
		
		$this->setOutput($page, 'page');
		$this->setOutput($perpage, 'perpage');
		$this->setOutput($count, 'count');
		$this->setOutput($url, 'args');
	}

	/**
	 * 设置当前选项卡被选中
	 *
	 * @param string $action
	 *        	操作名
	 * @return void
	 */
	private function setCurrentTab($action) {
		$headerTab = array(
			'run' => '', 
			'strategy' => '', 
			'recharge' => '', 
			'exchange' => '', 
			'transfer' => '', 
			'log' => '');
		$headerTab[$action] = 'current';
		$this->setOutput($headerTab, 'currentTabs');
	}

	/**
	 * 获得积分服务
	 *
	 * @return PwCreditSetService
	 */
	private function _getCreditService() {
		return Wekit::load('credit.srv.PwCreditSetService');
	}

	/**
	 * 获得策略的服务对象
	 *
	 * @return PwCreditStrategyService
	 */
	private function _getCreditStrategyService() {
		return Wekit::load('credit.srv.PwCreditStrategyService');
	}
}