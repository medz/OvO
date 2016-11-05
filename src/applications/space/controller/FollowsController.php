<?php
Wind::import('APPS:space.controller.SpaceBaseController');
/**
 * the last known user to change this file in the repository <$LastChangedBy$>
 * 
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package
 *
 */
class FollowsController extends SpaceBaseController {

	/**
	 * 关注-首页
	 */
	public function run() {
		$type = $this->getInput('type');
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = 20;
		list($start, $limit) = Pw::page2limit($page, $perpage);
		$args = $classCurrent = array();
/*		$typeCounts = $this->_getTypeDs()->countUserType($this->space->spaceUid);
		if ($type) {
			$tmp = $this->_getTypeDs()->getUserByType($this->space->spaceUid, $type, $limit, $start);
			$follows = $this->_getDs()->fetchFollows($this->space->spaceUid, array_keys($tmp));
			$count = $typeCounts[$type] ? $typeCounts[$type]['count'] : 0;
			$classCurrent[$type] = 'current';
			$args = array('type' => $type);
		} else {*/
		$follows = $this->_getDs()->getFollows($this->space->spaceUid, $limit, $start);
		$count = $this->space->spaceUser['follows'];
		$classCurrent[0] = 'current';
		//}
		$uids = array_keys($follows);
		$fans = $this->_getDs()->fetchFans($this->loginUser->uid, $uids);
		$myfollows = $this->_getDs()->fetchFollows($this->loginUser->uid, $uids);
		$userList = Wekit::load('user.PwUser')->fetchUserByUid($uids, PwUser::FETCH_ALL);
		
		$service = $this->_getService();

		$args['uid'] = $this->space->spaceUid;
		$follows = WindUtility::mergeArray($follows, $userList);
		if (!$follows && $this->space->tome == PwSpaceBo::MYSELF) {
			$num = 20;
			$uids = $this->_getRecommendService()->getRecommendAttention($this->loginUser->uid,$num);
			$this->setOutput($this->_getRecommendService()->buildUserInfo($this->loginUser->uid, $uids, $num), 'recommend');
		}
		$this->setOutput($fans, 'fans');
		$this->setOutput($follows, 'follows');
		$this->setOutput($myfollows, 'myfollows');
		$this->setOutput($classCurrent, 'classCurrent');
		$this->setOutput($args, 'args');
		$this->setOutput($page, 'page');
		$this->setOutput($perpage, 'perpage');
		$this->setOutput($count, 'count');
		$this->setOutput('follows', 'src');
		
		// seo设置
		Wind::import('SRV:seo.bo.PwSeoBo');
		$seoBo = PwSeoBo::getInstance();
		$lang = Wind::getComponent('i18n');
		$seoBo->setCustomSeo(
			$lang->getMessage('SEO:space.follows.run.title', 
				array($this->space->spaceUser['username'], $this->space->space['space_name'])), '', 
			$lang->getMessage('SEO:space.follows.run.description', 
				array($this->space->spaceUser['username'])));
		Wekit::setV('seo', $seoBo);
	}

	protected function _getDs() {
		return Wekit::load('attention.PwAttention');
	}

	protected function _getTypeDs() {
		return Wekit::load('attention.PwAttentionType');
	}

	protected function _getService() {
		return Wekit::load('attention.srv.PwAttentionService');
	}
	
	/**
	 * PwAttentionRecommendFriendsService
	 *
	 * @return PwAttentionRecommendFriendsService
	 */
	protected function _getRecommendService() {
		return Wekit::load('attention.srv.PwAttentionRecommendFriendsService');
	}
}

?>