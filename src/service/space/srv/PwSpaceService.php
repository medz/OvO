<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwSpaceService.php 17060 2012-08-31 01:50:31Z gao.wanggao $ 
 * @package 
 */
 class PwSpaceService {

 	/**
 	 * 更新访问脚印
 	 * 
 	 * @param int $spaceUid
 	 * @param int $visitUid
 	 */
 	public function signVisitor($spaceUid, $visitUid = 0) {
 		if ($spaceUid < 1) return false;
 		if ($spaceUid == $visitUid) return false;
 		if ($visitUid < 1) return $this->_getSpaceDs()->updateNumber($spaceUid);
 		$time = Pw::getTime();
 		$space = $this->_getSpaceDs()->getSpace($spaceUid);
 		$visitors = unserialize($space['visitors']);
 		$visitors = is_array($visitors) ? $visitors : array();
 		if (array_key_exists($visitUid, $visitors)) {
 			$keys = array_keys($visitors);
 			if (array_shift($keys) == $visitUid) return false; //如果是第一个不需要更新
 			unset($visitors[$visitUid]);
 		}
 		
 		$visitors = array($visitUid=>$time) + $visitors;
 		if (count($visitors) > 20) $visitors = array_slice($visitors, 0, 20, true);
 		$space['visit_count']++;
 		Wekit::load('space.dm.PwSpaceDm');
 		$dm = new PwSpaceDm($spaceUid);
 		$dm->setVisitors($visitors)
 			->setVisitCount($space['visit_count']);
 		return $this->_getSpaceDs()->updateInfo($dm);
 	}
 	
 	/**
 	 * 更新我的脚印
 	 * 
 	 * @param int $spaceUid
 	 * @param int $visitUid
 	 */
 	public function signToVisitor($spaceUid, $visitUid) {
 		if ($spaceUid < 1 || $visitUid < 1) return false;
 		if ($spaceUid == $visitUid) return false;	
 		$time = Pw::getTime();
 		$space = $this->_getSpaceDs()->getSpace($visitUid);
 		$tovisitors = unserialize($space['tovisitors']);
 		$tovisitors = is_array($tovisitors) ? $tovisitors : array();
 		if (array_key_exists($spaceUid, $tovisitors)) {
 			$keys = array_keys($tovisitors);
 			if (array_shift($keys) == $spaceUid) return false; //如果是第一个不需要更新
 			unset($tovisitors[$spaceUid]);
 		}
 		
 		$tovisitors = array($spaceUid=>$time) + $tovisitors;
		if (count($tovisitors) > 20) $tovisitors = array_slice($tovisitors, 0, 20, true);
 		Wekit::load('space.dm.PwSpaceDm');
 		$dm = new PwSpaceDm($visitUid);
 		$dm->setTovisitors($tovisitors);
 		return $this->_getSpaceDs()->updateInfo($dm);
 	}
 	
	/**
	 * 根据生日中的月份和日期来计算所属星座
	 * Enter description here ...
	 * @param int $y 
	 * @param int $m
	 * @param int $d
	 */
	public function getConstellation($y,$m,$d){
		if (empty($y) || empty($m) || empty($d)) return 'no';
		$constellations = array( 'aquarius','pisces','aries','taurus','gemini','cancer','leo','virgo','libra','scorpio','sagittarius','capricorn' );
		if ($d <= 22) {
			if (1 != $m){
				$constellation = $constellations[$m-2];
			}else {
				$constellation = $constellations[11];
			}
		} else { 
			$constellation = $constellations[$m-1];
		}
		return $constellation;
	}
	
 	private function _getSpaceDs() {
 		return Wekit::load('space.PwSpace');
 	}

 }



?>