<?php

/**
 * @author Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwMiscLikeDo.php 20027 2012-10-22 11:49:23Z gao.wanggao $ 
 * @package 
 */
class PwMiscLikeDo{
	
	/* (non-PHPdoc)
	 * @see PwLikeDoBase::addLike()
	 */
	public function addLike(PwUserBo $userBo, PwLikeDm $dm) {
		$data = $dm->getData();
		$ds = Wekit::load('user.PwUserBehavior');
		return $ds->replaceBehavior($data['belikeuid'], 'belike_times');
	}

	public function delLike($uid, $beLikeUid) {
 		$behaviorDs = Wekit::load('user.PwUserBehavior');
		$behavior = $behaviorDs->getBehavior($beLikeUid, 'belike_times');
		return $behaviorDs->replaceInfo($beLikeUid, 'belike_times', (int)$behavior['number'] -1 , $behavior['extend_info']);
	}
}
?>