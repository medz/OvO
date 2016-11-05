<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('APPS:.profile.controller.BaseProfileController');
Wind::import('SRV:credit.bo.PwCreditBo');

/**
 * 积分相关查询
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: CreditController.php 24975 2013-02-27 09:24:54Z jieyin $
 * @package src.products.u.controller.profile
 */
class CreditController extends BaseProfileController {
	
	/* (non-PHPdoc)
	 * @see BaseProfileController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		$this->setCurrentLeft('credit');
	}
	
	/**
	 *  积分首页--我的积分
	 */
	public function run() {
		//支持积分转换的积分
		$exchange = array();
		$exchange_config = Wekit::C()->credit->get('exchange', array());
		foreach ($exchange_config as $key => $value) {
			if ($value['ifopen']) $exchange[$value['credit1']][] = $value;
		}
		
		//用户积分升级进度
		$totalCredit = Wekit::load('usergroup.srv.PwUserGroupsService')->getCredit($this->loginUser->info);
		$_cache = Wekit::cache()->get('level');
		$lneed = $_cache['lneed'];
		arsort($lneed);
		reset($lneed);
		$memberid = $nextid = $cpoint = $npoint = 0;
		foreach ($lneed as $key => $value) {
			$memberid = $key;
			$cpoint = $value;
			if ($totalCredit >= $value) break;
			$nextid = $key;
			$npoint = $value;
		}
		if ($totalCredit < $cpoint) {
			$max = max(abs($totalCredit), abs($cpoint)) * 2;
			$rate = round(($max - abs($totalCredit - $cpoint)) / $max * 10);
			$nextid = $npoint = 0;
		} elseif ($nextid) {
			$rate = round(($totalCredit - $cpoint) / ($npoint - $cpoint) * 100);
		} else {
			$rate = round(($totalCredit - $cpoint) / ($totalCredit * 2) * 100);
		}

		//综合积分： 计算方案
		$upgrade = Wekit::C('site', 'upgradestrategy');
		$_upgrade = array();
		if ($upgrade['postnum']) {
			$_upgrade[] = '发帖数X' . $upgrade['postnum'];
		}
		if ($upgrade['digest']) {
			$_upgrade[] = '精华X' . $upgrade['digest'];
		}
		
		/* @var $creditBo PwCreditBo */
		$creditBo = PwCreditBo::getInstance();
		foreach ($creditBo->cType as $k => $v) {
			if (!$upgrade['credit' . $k]) continue;
			$_upgrade[] = $v . 'X' . $upgrade['credit' . $k];
		}
		if ($upgrade['onlinetime']) {
			$_upgrade[] = '会员历史在线时间X' . $upgrade['onlinetime'];
		}
		$_upgrade = implode(' + ', $_upgrade);
		
		$this->setOutput($_upgrade, '_upgrade');
		$this->setOutput($creditBo, 'creditBo');
		$this->setOutput(Wekit::C('credit', 'transfer'), 'transfer');
		$this->setOutput($exchange, 'exchange');

		$this->setOutput($totalCredit, 'totalCredit');
		$this->setOutput($cpoint, 'cpoint');
		$this->setOutput($npoint, 'npoint');
		$this->setOutput($memberid, 'memberid');
		$this->setOutput($nextid, 'nextid');
		$this->setOutput($rate, 'rate');
		$this->setOutput($_cache['ltitle'], 'ltitle');

		$this->setTemplate('profile_credit');
		
		// seo设置
		Wind::import('SRV:seo.bo.PwSeoBo');
		$seoBo = PwSeoBo::getInstance();
		$lang = Wind::getComponent('i18n');
		$seoBo->setCustomSeo($lang->getMessage('SEO:profile.credit.run.title'), '', '');
		Wekit::setV('seo', $seoBo);
	}

	/**
	 * 积分转换
	 */
	public function doexchangeAction() {
		list($credit1, $credit2, $num) = $this->getInput(array('credit1', 'credit2', 'num'), 'post');
		$exchange = Wekit::C('credit', 'exchange');
		$key = $credit1 . '_' . $credit2;
		//是否可以转换
		if (!isset($exchange[$key]) || !$exchange[$key]['ifopen']) {
			$this->showError('CREDIT:exchange.fail.exists.not');
		}
		//转换的数量必须是设置的数量的整数倍
		if ($num < $exchange[$key]['value1'] || ($num % $exchange[$key]['value1']) != 0) {
			$this->showError(array('CREDIT:exchange.fail.num.error', array('{num}' => $exchange[$key]['value1'])));
		}
		//如果用户当前该积分的数量小于设置转换的数量
		if ($this->loginUser->getCredit($credit1) < $num) {
			$this->showError(array('CREDIT:exchange.fail.credit.less', array('{credit}' => $this->loginUser->getCredit($credit1))));
		}
		$rate = intval($num / $exchange[$key]['value1']);
		$income = $rate * $exchange[$key]['value2'];
		
		/* @var $creditBo PwCreditBo */
		$creditBo = PwCreditBo::getInstance();
		$creditBo->addLog('exchange_out', array($credit1 => -$num), $this->loginUser);
		$creditBo->addLog('exchange_in', array($credit2 => $income), $this->loginUser);
		$creditBo->sets($this->loginUser->uid, array(
			$credit1 => -$num,
			$credit2 => $income
		));
		
		//发送通知
		$params = array();
		$params['credit1'] = $creditBo->cType[$credit1];
		$params['unit1'] = $creditBo->cUnit[$credit1];
		$params['num1'] = $num;
		$params['credit2'] = $creditBo->cType[$credit2];
		$params['unit2'] = $creditBo->cUnit[$credit2];
		$params['num2'] = $income;
		$params['change_type'] = 'exchange';
		/* @var $notice PwNoticeService */
		$notice = Wekit::load('SRV:message.srv.PwNoticeService');
		$notice->sendNotice($this->loginUser->uid, 'credit', $this->loginUser->uid, $params);
		
		$this->showMessage('success');
	}

	/**
	 * 积分转账
	 */
	public function dotransferAction() {
		list($touser, $num, $credit, $password) = $this->getInput(array('touser', 'num', 'credit', 'pwd'), 'post');
		//验证密码是否正确
		/* @var $userSrv PwUserService */
		$userSrv = Wekit::load('user.srv.PwUserService');
		if (($r = $userSrv->verifyUser($this->loginUser->uid, $password)) instanceof PwError) {
			$this->showError('CREDIT:transfer.fail.pwd.error');
		}
		
		$transfer = Wekit::C('credit', 'transfer');
		//该积分是否支持转账
		if (!isset($transfer[$credit]) || !$transfer[$credit]['ifopen']) {
			$this->showError('CREDIT:transfer.fail.credit.exists.not');
		}
		//适合符合最低转换条件
		$num = intval($num);
		if ($num < $transfer[$credit]['min']) {
			$this->showError(array('CREDIT:transfer.fail.num.error', array('{num}' => $transfer[$credit]['min'])));
		}
		//目标用户是否合法--可以转给自己
		/* @var $userDs PwUser */
		$userDs = Wekit::load('user.PwUser');
		$toUserInfo = $userDs->getUserByName($touser, PwUser::FETCH_MAIN);
		if (!$toUserInfo) {
			$this->showError('CREDIT:transfer.fail.touser.exists.not');
		}
		
		$outCome = floor($num * $transfer[$credit]['rate'] / 100) + $num;
		//用户积分数量不足以转账num个
		if ($this->loginUser->getCredit($credit) < $outCome) {
			$this->showError(array('CREDIT:transfer.fail.credit.less', array('{credit}' => $this->loginUser->getCredit($credit))));
		}
		
		/* @var $creditBo PwCreditBo */
		$creditBo = PwCreditBo::getInstance();
		$creditBo->addLog('transfer_out', array($credit => -$outCome), $this->loginUser, array('tousername' => $toUserInfo['username']));
		$creditBo->addLog('transfer_in', array($credit => $num), new PwUserBo($toUserInfo['uid']), array('fromusername' => $this->loginUser->username));
		$creditBo->sets($this->loginUser->uid, array($credit => -$outCome));
		$creditBo->sets($toUserInfo['uid'], array($credit => $num));
		
		//发送通知
		$params = array();
		$params['fromUid'] = $this->loginUser->uid;
		$params['fromUserName'] = $this->loginUser->username;
		$params['credit'] = $creditBo->cType[$credit];
		$params['unit'] = $creditBo->cUnit[$credit];
		$params['num'] = $num;
		$params['change_type'] = 'transfer';
		/* @var $notice PwNoticeService */
		$notice = Wekit::load('SRV:message.srv.PwNoticeService');
		$notice->sendNotice($toUserInfo['uid'], 'credit', $toUserInfo['uid'], $params);
		
		$this->showMessage('success');
	}

	/**
	 * 积分充值
	 */
	public function rechargeAction() {
		$config = Wekit::C('pay');
		if (!$config['ifopen']) {
			$this->showError($config['reason']);
		}
		$recharge = Wekit::C('credit', 'recharge');
		$creditBo = PwCreditBo::getInstance();
		foreach ($recharge as $key => $value) {
			if (!isset($creditBo->cType[$key])) {
				unset($recharge[$key]);
			}
		}
		$this->setOutput($recharge, 'recharge');
		$this->setOutput($creditBo, 'creditBo');
		$this->setTemplate('profile_credit_recharge');
	}

	/**
	 * 现金充值
	 */
	public function payAction() {
		$config = Wekit::C('pay');
		if (!$config['ifopen']) {
			$this->showError($config['reason']);
		}

		list($credit, $pay, $paymethod) = $this->getInput(array('credit', 'pay', 'paymethod'));
		
		if (!in_array($paymethod, array('1', '2', '3', '4'))) {
			$this->showError('onlinepay.paymethod.select');
		}
		$onlinepay = Wekit::load('pay.srv.PwPayService')->getPayMethod($paymethod);
		if (($result = $onlinepay->check()) instanceof PwError) {
			$this->showError($result->getError());
		}

		$recharge = Wekit::C('credit', 'recharge');
		$creditBo = PwCreditBo::getInstance();
		if (!isset($recharge[$credit]) || !isset($creditBo->cType[$credit])) {
			$this->showError('CREDIT:pay.type.error');
		}
		$pay = round($pay,2);
		$min = max(0, $recharge[$credit]['min']);
		if ($pay < $min) {
			$this->showError(array('CREDIT:pay.num.min', array('{min}' => $min)));
		}
		$creditName = $creditBo->cType[$credit];
		$order_no = $onlinepay->createOrderNo();
		
		Wind::import('SRV:pay.dm.PwOrderDm');
		$dm = new PwOrderDm();
		$dm->setOrderNo($order_no)
			->setPrice($pay)
			->setNumber(1)
			->setState(0)
			->setPaytype(1)
			->setBuy($credit)
			->setCreatedUserid($this->loginUser->uid)
			->setCreatedTime(Pw::getTime());
		Wekit::load('pay.PwOrder')->addOrder($dm);
		
		Wind::import('SRV:pay.vo.PwPayVo');
		$vo = new PwPayVo();
		$vo->setOrderNo($order_no)
			->setFee($pay)
			->setTitle('积分充值(订单号：' . $order_no . ')')
			->setBody('购买论坛' . $creditName . '(论坛UID：' . $this->loginUser->uid . ')');
		
		$this->setOutput(array('url' => $onlinepay->getUrl($vo)), 'data');//todo WindUrlHelper 改进
		$this->showMessage('success');
		//$this->showMessage('success', $onlinepay->getUrl($vo));
		//$this->forwardRedirect($onlinepay->getUrl($vo));
		
	}

	/**
	 * 现金充值记录
	 */
	public function orderAction() {
		$config = Wekit::C('pay');
		if (!$config['ifopen']) {
			$this->showError($config['reason']);
		}
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = 20;
		list($start, $limit) = Pw::page2limit($page, $perpage);

		$count = Wekit::load('pay.PwOrder')->countByUidAndType($this->loginUser->uid, 1);
		$order = Wekit::load('pay.PwOrder')->getOrderByUidAndType($this->loginUser->uid, 1, $limit, $start);

		$this->setOutput(PwCreditBo::getInstance(), 'creditBo');
		$this->setOutput($order, 'order');
		$this->setOutput($page, 'page');
		$this->setOutput($perpage, 'perpage');
		$this->setOutput($count, 'count');
		$this->appendBread('积分充值', WindUrlHelper::createUrl('profile/credit/recharge'));
        $this->appendBread('订单记录', WindUrlHelper::createUrl('profile/credit/order'));
		$this->setTemplate('profile_credit_order');
	}

	/**
	 * 积分日志
	 */
	public function logAction() {
		list($ctype, $timeStart, $timeEnd, $award) = $this->getInput(array('ctype', 'time_start', 'time_end', 'award'));
		$page = $this->getInput('page');
		$page < 1 && $page = 1;
		$perpage = 20;
		list($offset, $limit) = Pw::page2limit($page, $perpage);

		Wind::import('SRV:credit.srv.PwCreditOperationConfig');
		Wind::import('SRV:credit.vo.PwCreditLogSc');
		
		$sc = new PwCreditLogSc();
		$url = array();
		if ($ctype) {
			$sc->setCtype($ctype);
			$url['ctype'] = $ctype;
		}
		if ($timeStart) {
			$sc->setCreateTimeStart(Pw::str2time($timeStart));
			$url['time_start'] = $timeStart;
		}
		if ($timeEnd) {
			$sc->setCreateTimeEnd(Pw::str2time($timeEnd));
			$url['time_end'] = $timeEnd;
		}
		if ($award) {
			$sc->setAward($award);
			$url['award'] = $award;
		}
		if ($sc->hasData()) {
			$sc->setUserid($this->loginUser->uid);
			$count = Wekit::load('credit.PwCreditLog')->countBySearch($sc);
			$log = Wekit::load('credit.PwCreditLog')->searchLog($sc, $limit, $offset);
		} else {
			$count = Wekit::load('credit.PwCreditLog')->countLogByUid($this->loginUser->uid);
			$log = Wekit::load('credit.PwCreditLog')->getLogByUid($this->loginUser->uid, $limit, $offset);
		}
		
		$this->setOutput($log, 'log');
		$this->setOutput(PwCreditBo::getInstance(), 'creditBo');
		$this->setOutput(PwCreditOperationConfig::getInstance(), 'coc');

		$this->setOutput($page, 'page');
		$this->setOutput($perpage, 'perpage');
		$this->setOutput($count, 'count');
		$this->setOutput($url, 'url');

		$this->setOutput($ctype, 'ctype');
		$this->setOutput($timeStart, 'timeStart');
		$this->setOutput($timeEnd, 'timeEnd');
		$this->setOutput($award, 'award');

		$this->setTemplate('profile_credit_log');
	}
}
