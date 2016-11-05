<?php
Wind::import('SRV:invite.vo.PwInviteCodeSo');
/**
 * 邀请好友
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: InviteController.php 23994 2013-01-18 03:51:46Z long.shi $
 * @package service.products.bbs.controller
 */
class InviteController extends PwBaseController {
	private $regist = array();
	
	/* (non-PHPdoc)
	 * @see PwBaseController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		if (!$this->loginUser->isExists()) {
			$this->forwardRedirect(WindUrlHelper::createUrl('u/login/run', array('backurl' => WindUrlHelper::createUrl('my/invite/run'))));
		}
		$this->regist = Wekit::C('register');
		$this->setOutput('invite', 'li');		
	}

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		if ($this->regist['type'] != 2) {
			$this->forwardRedirect(WindUrlHelper::createUrl('my/invite/inviteFriend'));
//			$this->showError('USER:invite.close');
		}
		
		Wind::import('SRV:credit.bo.PwCreditBo');
		/* @var $pwCreditBo PwCreditBo */
		$pwCreditBo = PwCreditBo::getInstance();
		
		$startTime = Pw::str2time(Pw::time2str(Pw::getTime(), 'Y-m-d'));
		$readyBuy = $this->_getDs()->countByUidAndTime($this->loginUser->uid, $startTime);
		$gidLimit = abs(ceil($this->loginUser->getPermission('invite_limit_24h')));
		$price = abs(ceil($this->loginUser->getPermission('invite_buy_credit_num')));
		
		$_tmpId = $this->regist['invite.credit.type'];
		$_credit = array('id' => $_tmpId, 'name' => $pwCreditBo->cType[$_tmpId], 'unit' => $pwCreditBo->cUnit[$_tmpId]);
		$this->setOutput($_credit, 'creditWithBuy');//用于购买的积分信息
		
		$_tmpId = $this->regist['invite.reward.credit.type'];
		$_credit = array('id' => $_tmpId, 'name' => $pwCreditBo->cType[$_tmpId], 'unit' => $pwCreditBo->cUnit[$_tmpId]);
		$this->setOutput($_credit, 'rewardCredit');//奖励的积分信息
		
		$this->setOutput($readyBuy > $gidLimit ? 0 : ($gidLimit - $readyBuy), 'canBuyNum');//还能购买的邀请数
		$this->setOutput($price, 'pricePerCode');//每个邀请码需要积分的单价
		$this->setOutput($this->loginUser->info['credit' . $this->regist['invite.credit.type']], 'myCredit');//我拥有的积分
		$this->setOutput($this->regist['invite.reward.credit.num'], 'rewardNum');//奖励积分数
		$this->setOutput($this->regist['invite.expired'], 'codeExpired');//邀请码有效期
		$this->setOutput($this->loginUser->getPermission('invite_allow_buy'), 'canInvite');//该用户组是否可以购买邀请码
		$this->setOutput($this->regist['invite.pay.money'], 'money');
		$this->setOutput(/*$this->regist['invite.pay.open']*/ false, 'canBuyWithMoney');
		
		$this->listCode();
		
		// seo设置
		Wind::import('SRV:seo.bo.PwSeoBo');
		$seoBo = PwSeoBo::getInstance();
		$lang = Wind::getComponent('i18n');
		$seoBo->setCustomSeo($lang->getMessage('SEO:bbs.invite.run.title'), '', '');
		Wekit::setV('seo', $seoBo);
	}
	
	/**
	 * 购买邀请码
	 */
	public function buyAction() {
		if (!$this->loginUser->getPermission('invite_allow_buy')) $this->showError('USER:invite.buy.forbidden');
		$num = $this->getInput('num', 'post');
		/* @var $service PwInviteCodeService */
		$service = Wekit::load('invite.srv.PwInviteCodeService');
		$result = $service->buyInviteCodes($this->loginUser, $num, $this->regist['invite.credit.type']);
		if ($result instanceof PwError) $this->showError($result->getError());
		$this->showMessage('USER:invite.buy.success');
	}
	
	/**
	 * 在线购买
	 */
	public function onlineAction() {
		
	}
	
	/**
	 * 判断是否可以购买如此数量的邀请码
	 */
	public function allowBuyAction() {
		if (!$this->loginUser->getPermission('invite_allow_buy')) $this->showError('USER:invite.buy.forbidden');
		$num = $this->getInput('num', 'post');
		/* @var $service PwInviteCodeService */
		$service = Wekit::load('invite.srv.PwInviteCodeService');
		$result = $service->allowBuyInviteCode($this->loginUser, $num, $this->regist['invite.credit.type']);
		if ($result instanceof PwError) $this->showError($result->getError());
		$this->showMessage();
	}
	
	/**
	 * 邀请统计页面
	 */
	public function statisticsAction() {
		$page = intval($this->getInput('page'));
		$perpage = 18;
		$page || $page = 1;
		$count = $this->_getDs()->countUsedCodeByCreatedUid($this->loginUser->uid);
		$list = array();
		if ($count > 0) {
			$totalPage = ceil($count / $perpage);
			$page > $totalPage && $page = $totalPage;
			list($start, $limit) = Pw::page2limit($page, $perpage);
			$list = $this->_getDs()->getUsedCodeByCreatedUid($this->loginUser->uid, $limit, $start);
			$invitedUids = array_keys($list);
			/* @var $userDs PwUser */
			$userDs = Wekit::load('user.PwUser');
			$list = $userDs->fetchUserByUid($invitedUids);
		}
		
		Wind::import('SRV:credit.bo.PwCreditBo');
		/* @var $pwCreditBo PwCreditBo */
		$pwCreditBo = PwCreditBo::getInstance();
		
		$_tmpid = $this->regist['invite.reward.credit.type'];
		$_credit = array('id' => $_tmpid, 'name' => $pwCreditBo->cType[$_tmpid], 'unit' => $pwCreditBo->cUnit[$_tmpid]);
		$this->setOutput($_credit, 'rewardCredit');//奖励的积分信息
		
		$this->setOutput($this->regist['invite.reward.credit.num'], 'rewardNum');//奖励积分数
		$this->setOutput($list, 'list');
		$this->setOutput($count, 'count');
		$this->setOutput($page, 'page');
		$this->setOutput($perpage, 'perpage');
	}
	
	/**
	 * 邀请链接
	 */
	public function inviteFriendAction() {
		if ($this->regist['type'] == 2) {
			$this->forwardAction('my/invite/run');
		}
		
		/* @var $pwInviteUrlLogSrv PwInviteFriendService */
		$pwInviteUrlLogSrv = Wekit::load('invite.srv.PwInviteFriendService');
		$invite = $pwInviteUrlLogSrv->createInviteCode($this->loginUser->uid);
		$this->setOutput(WindUrlHelper::createUrl('u/register/run', array('invite' => $invite)), 'url');
		$this->setTemplate('invite_friend');
		
		// seo设置
		Wind::import('SRV:seo.bo.PwSeoBo');
		$seoBo = PwSeoBo::getInstance();
		$lang = Wind::getComponent('i18n');
		$seoBo->setCustomSeo($lang->getMessage('SEO:bbs.invite.run.title'), '', '');
		Wekit::setV('seo', $seoBo);
	}
	
	/**
	 * 列出用户拥有的邀请码
	 */
	private function listCode() {
		$perpage = 20;
		list($type, $page) = $this->getInput(array('type', 'page'), 'get');
		$vo = new PwInviteCodeSo();
		$vo->setCreatedUid($this->loginUser->uid)
			->setIfused(0)//未使用
			->setExpireTime(Pw::getTime() - ($this->regist['invite.expired'] * 86400));//未过期
		$count = $this->_getDs()->countSearchCode($vo);
		$list = array();
		if ($count) {
			$totalPage = ceil($count/$perpage);
			$page = intval($page);
			$page = $page < 1 ? 1 : ($page > $totalPage ? $totalPage : $page);
			list($start, $limit) = Pw::page2limit($page, $perpage);
			
			/* @var $service PwInviteCodeService */
			$service = Wekit::load('invite.srv.PwInviteCodeService');
			$list = $service->searchInvitecodeList($vo, $limit, $start);
		}
		$this->setOutput($perpage, 'perpage');
		$this->setOutput($count, 'count');
		$this->setOutput($list, 'list');
		$this->setOutput($page, 'page');
		$this->setOutput($type, 'type');
	}
	
	/**
	 * 获得邀请码DS
	 *
	 * @return PwInviteCode
	 */
	private function _getDs() {
		return Wekit::load('invite.PwInviteCode');
	}
}