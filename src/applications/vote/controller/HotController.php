<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:poll.srv.PwPollDisplay');
Wind::import('SRV:poll.srv.dataSource.PwFetchPollByOrder');

/**
 * 应用中心热门投票模型
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com> 2012-01-12
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: HotController.php 3219 2012-01-12 06:43:45Z mingxing.sun $
 * @package admin
 * @subpackage controller
 */

class HotController extends PwBaseController {
	
	public $page = 1;
	public $perpage = 10;
	
	public function run() {
		$page = $this->getInput('page'); 
		$this->page = $page < 1 ? 1 : intval($page);
		list($start, $limit) = Pw::page2limit($this->page, $this->perpage);
		
		$timestamp = PW::getTime();
		$startTime = $timestamp- (7 * 86400);
		$endTime = $timestamp;
		
		$total = $this->_getPollDs()->countPollByTime($startTime, $endTime);
		
		$pollInfo = array();
		
		if ($total) {
			Wind::import('SRV:poll.srv.dataSource.PwFetchPollByTime');
			$pollDisplay = new PwPollDisplay(new PwFetchPollByTime($startTime, $endTime, $limit, $start, array('voter_num'=>0,'created_time'=>0)));
			$pollInfo = $this->_buildPoll($pollDisplay->gather());
		}

		$latestPollDisplay = new PwPollDisplay(new PwFetchPollByOrder(10, 0, array('created_time'=>'0')));
		$latestPoll = $latestPollDisplay->gather();
		
		$this->setOutput($total, 'total');
		$this->setOutput($pollInfo, 'pollInfo');
		$this->setOutput($latestPoll, 'latestPoll');
		$this->setOutput($this->page, 'page');
		$this->setOutput($this->perpage, 'perpage');
		$this->setOutput(
			array(
				'allowview' => $this->loginUser->getPermission('allow_view_vote'),
				'allowvote'=> $this->loginUser->getPermission('allow_participate_vote')
			)
		, 'pollGroup');
		
		// seo设置
		Wind::import('SRV:seo.bo.PwSeoBo');
		$seoBo = PwSeoBo::getInstance();
		$lang = Wind::getComponent('i18n');
		if ($this->page > 1) {
			$seoBo->setCustomSeo($lang->getMessage('SEO:vote.hot.run.page.title', array($this->page)), $lang->getMessage('vote.hot.run.description'), '');
		} else {
			$seoBo->setCustomSeo($lang->getMessage('SEO:vote.hot.run.title'), '', $lang->getMessage('SEO:vote.hot.run.description'));
		}
		Wekit::setV('seo', $seoBo);
	}
	
	private function _buildPoll($data) {
		$pollid = $myPollid = $reuslt = array();

		foreach ($data as $value) {
			$pollid[] = $value['poll_id'];
		}

		$loginUserPollids = $this->_getPollVoterDs()->getPollByUidAndPollid($this->loginUser->uid, $pollid);

		foreach ($data as $value) {
			$value['isvoted'] = in_array($value['poll_id'], $loginUserPollids)  ? true : false;
			$reuslt[] = $value;
		}
		
		return $reuslt;
	}
	
	/**
	 * get PwPollService
	 *
	 * @return PwPollService
	 */
	protected function _getPollService(){
		return Wekit::load('poll.srv.PwPollService');
	}
	
	/**
	 * get PwPollVoter
	 *
	 * @return PwPollVoter
	 */
	protected function _getPollVoterDs(){
		return Wekit::load('poll.PwPollVoter');
	}
	
	/**
	 * get PwPoll
	 *
	 * @return PwPoll
	 */
	protected function _getPollDs(){
		return Wekit::load('poll.PwPoll');
	}
}