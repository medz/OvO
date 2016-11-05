<?php
Wind::import('SRV:report.srv.report.PwReportAction');

class PwReportThread extends PwReportAction{
	
	protected $fid = 0;
	
	public function buildDm($type_id) {
		$threadDs = Wekit::load('forum.PwThread');
		$result = $threadDs->getThread($type_id);
		if (!$result) {
			return false;
		}
		$content = Pw::substrs($result['subject'], 20);
		$hrefUrl = WindUrlHelper::createUrl('bbs/read/run', array('tid' => $result['tid'], 'fid' => $result['fid']));
		$this->fid = $result['fid'];
		$dm = new PwReportDm();
		$dm->setContent($content)
			->setContentUrl($hrefUrl)
			->setAuthorUserid($result['created_userid']);
		return $dm;
	}
	
	public function getExtendReceiver() {
		$forumDs = Wekit::load('forum.PwForum');
		$forumInfo = $forumDs->getForum($this->fid);
		$manager = explode(',', $forumInfo['manager']);
		return array_keys($this->_getUserDs()->fetchUserByName($manager));
	}
	
	/**
	 * @return PwUser
	 */
	protected function _getUserDs(){
		return Wekit::load('user.PwUser');
	}
}