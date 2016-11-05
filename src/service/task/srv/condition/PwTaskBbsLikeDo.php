<?php
Wind::import('SRV:task.srv.PwTaskComplete');
Wind::import('SRV:task.srv.base.PwTaskCompleteInterface');
/**
 * 喜欢帖子的扩展
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskBbsLikeDo.php 20491 2012-10-30 08:15:09Z xiaoxia.xuxx $
 * @package service.task.srv.condition
 */
class PwTaskBbsLikeDo implements PwTaskCompleteInterface {
	
	private $fid;
	
	/* (non-PHPdoc)
	 * @see PwLikeDoBase::addLike()
	 */
	public function addLike(PwUserBo $userBo, PwLikeDm $dm) {
		if (!Wekit::C('site', 'task.isOpen')) return true;
		$data = $dm->getData();
		if ($data['typeid'] != PwLikeContent::THREAD) return false;
		/* @var $dm PwLikeDm */
		$bp = new PwTaskComplete($userBo->uid, $this);
		$thread = Wekit::load('forum.PwThread')->getThread($data['fromid']);
		$this->fid = $thread['fid'];
		$bp->doTask('bbs', 'like');
	}
	
	/* (non-PHPdoc)
	 * @see PwTaskCompleteInterface::doTask()
	 */
	public function doTask($conditions, $step) {
		if ($conditions['fid'] != $this->fid) return false;
		$isComplete = false;
		if (isset($step['percent']) && $step['percent'] == '100%') {
			$isComplete = true;
		} else {
			$step['current'] = isset($step['current']) ? intval($step['current'] + 1) : 1;
			$step['percent'] = intval($step['current'] / $conditions['num'] * 100) . '%';
			$step['percent'] == '100%' && $isComplete = true;
		}
		return array('isComplete' => $isComplete, 'step' => $step);
	}
}
?>