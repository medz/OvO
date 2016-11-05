<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 出售帖 / 帖子购买
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: BuythreadController.php 28868 2013-05-28 04:06:20Z jieyin $
 * @package forum
 */

class BuythreadController extends PwBaseController {

	public function run() {

		
	}

	public function recordAction() {
		list($tid, $pid, $page) = $this->getInput(array('tid', 'pid', 'page'));
		$perpage = 10;
		$page < 1 && $page = 1;
		list($offset, $limit) = Pw::page2limit($page, $perpage);
		$count = Wekit::load('forum.PwThreadBuy')->countByTidAndPid($tid, $pid);
		if (!$count) {
			$this->showError('BBS:thread.buy.error.norecord');
		}
		Wind::import('SRV:credit.bo.PwCreditBo');
		$record = Wekit::load('forum.PwThreadBuy')->getByTidAndPid($tid, $pid, $limit, $offset);
		$users = Wekit::load('user.PwUser')->fetchUserByUid(array_keys($record));
			
		$data = array();
		$cType = PwCreditBo::getInstance()->cType;
		foreach ($record as $key => $value) {
			$data[] = array(
				'uid' => $value['created_userid'],
				'username' => $users[$value['created_userid']]['username'],
				'cost' => $value['cost'],
				'ctype' => $cType[$value['ctype']],
				'created_time' => Pw::time2str($value['created_time'])
			);
		}
		$totalpage = ceil($count / $perpage);
		$nextpage = $page+1;
		$nextpage = $nextpage > $totalpage ? $totalpage : $nextpage;
		
		$this->setOutput(array('data' => $data, 'totalpage' => $totalpage, 'page' => $nextpage), 'data');
		$this->showMessage('success');
	}

	public function buyAction() {
		
		list($tid, $pid) = $this->getInput(array('tid', 'pid'));
		$submit = (int)$this->getInput('submit', 'post');
		if (!$this->loginUser->isExists()) {
			$this->showError('login.not');
		}
		if (!$tid) {
			$this->showError('data.error');
		}
		if ($pid) {
			$result = Wekit::load('forum.PwThread')->getPost($pid);
		} else {
			$pid = 0;
			$result = Wekit::load('forum.PwThread')->getThread($tid, PwThread::FETCH_ALL);
		}
		if (empty($result) || $result['tid'] != $tid) {
			$this->showError('data.error');
		}
		$start = strpos($result['content'], '[sell=');
		if ($start === false) {
			$this->showError('BBS:thread.buy.error.sell.not');
		}
		$start += 6;
		$end = strpos($result['content'], ']', $start);
		$cost = substr($result['content'], $start, $end - $start);

		list($creditvalue, $credittype) = explode(',', $cost);
		Wind::import('SRV:credit.bo.PwCreditBo');
		$creditBo = PwCreditBo::getInstance();
		isset($creditBo->cType[$credittype]) || $credittype = key($creditBo->cType);
		$creditType = $creditBo->cType[$credittype];
		if ($result['created_userid'] == $this->loginUser->uid) {
			$this->showError('BBS:thread.buy.error.self');
		}
		if (Wekit::load('forum.PwThreadBuy')->get($tid, $pid, $this->loginUser->uid)) {
			$this->showError('BBS:thread.buy.error.already');
		}
		
		if (($myCredit = $this->loginUser->getCredit($credittype)) < $creditvalue) {
			$this->showError(array('BBS:thread.buy.error.credit.notenough',array('{myCredit}' => $myCredit.$creditType, '{count}' => $creditvalue.$creditType)));
		}
		
		!$submit && $this->showMessage(array('BBS:thread.buy.message.buy', array('{count}' => $myCredit.$creditType, '{buyCount}' => -$creditvalue.$creditType)));
		Wind::import('SRV:forum.dm.PwThreadBuyDm');
		$dm = new PwThreadBuyDm();
		$dm->setTid($tid)
			->setPid($pid)
			->setCreatedUserid($this->loginUser->uid)
			->setCreatedTime(Pw::getTime())
			->setCtype($credittype)
			->setCost($creditvalue);
		Wekit::load('forum.PwThreadBuy')->add($dm);

		$creditBo->addLog('buythread', array($credittype => -$creditvalue), $this->loginUser, array(
			'title' => $result['subject'] ? $result['subject'] : Pw::substrs($result['content'], 20)
		));
		$creditBo->set($this->loginUser->uid, $credittype, -$creditvalue, true);
		
		$user = new PwUserBo($result['created_userid']);
		if (($max = $user->getPermission('sell_credit_range.maxincome')) && Wekit::load('forum.PwThreadBuy')->sumCost($tid, $pid) > $max) {

		} else {
			$creditBo->addLog('sellthread', array($credittype => $creditvalue), $user, array(
				'title' => $result['subject'] ? $result['subject'] : Pw::substrs($result['content'], 20)
			));
			$creditBo->set($user->uid, $credittype, $creditvalue, true);
		}
		$creditBo->execute();
		
		if ($pid) {
			Wind::import('SRV:forum.dm.PwReplyDm');
			$dm = new PwReplyDm($pid);
			$dm->addSellCount(1);
			Wekit::load('forum.PwThread')->updatePost($dm);
		} else {
			Wind::import('SRV:forum.dm.PwTopicDm');
			$dm = new PwTopicDm($tid);
			$dm->addSellCount(1);
			Wekit::load('forum.PwThread')->updateThread($dm, PwThread::FETCH_CONTENT);
		}

		$this->showMessage('success', 'bbs/read/run/?tid=' . $tid . '&fid=' . $result['fid'], true);
	}
}