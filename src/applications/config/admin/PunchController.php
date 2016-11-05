<?php
Wind::import('ADMIN:library.AdminBaseController');

/**
 * 后台设置-每日打卡设置
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PunchController extends AdminBaseController {

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$config = Wekit::C()->getValues('site');
		Wind::import('SRV:credit.bo.PwCreditBo');
		$transfer = Wekit::C('credit', 'transfer');
		$this->setOutput(PwCreditBo::getInstance(), 'creditBo');
		$this->setOutput($config, 'config');
	}

	/**
	 * 后台设置-每日打卡设置
	 */
	public function dorunAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		$config = new PwConfigSet('site');
		list($punchOpen,$punchReward,$punchFrendOpen,$punchFrendReward) = $this->getInput(array('punchOpen','punchReward','punchFrendOpen','punchFrendReward'), 'post');
		$oldmin = abs(ceil($punchReward['min']));
		$oldmax = abs(ceil($punchReward['max']));
		$min = $oldmin;
		$max = $oldmax;
		if ($oldmin > $oldmax) {
			$min = $oldmax;
			$max = $oldmin;
		}
		$punchReward = array(
			'type' => $punchReward['type'],
			'min' => $min,
			'max' => $max,
			'step' => abs(ceil($punchReward['step'])),
		);	
		$rewardNum = abs(ceil($punchFrendReward['rewardNum']));
		$punchFrendReward = array(
			'friendNum' => abs(ceil($punchFrendReward['friendNum'])),
			'rewardMeNum' => abs(ceil($punchFrendReward['rewardMeNum'])),
			'rewardNum' => $rewardNum > $max ? $max : $rewardNum,
		);
		$config->set('punch.open', $punchOpen)
			->set('punch.reward', $punchReward)
			->set('punch.friend.open', $punchFrendOpen)
			->set('punch.friend.reward', $punchFrendReward)
			->flush();
		$this->showMessage('ADMIN:success');
	}
}
?>