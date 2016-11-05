<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$ 
 * @package 
 */
class PwCron {
	
	const NOOPEN = 0;
	const ISOPEN = 1;
	const SYSTEM = 2;
	
	public function getCron($cronId) {
		$cronId = (int)$cronId;
		if ($cronId < 1) return array();
		return $this->_getDao()->getCron($cronId);
	}
	
	/**
	 * 用于对系统任务的判断
	 * 
	 * @param string $cronFile
	 */
	public function getCronByFile($cronFile) {
		if (!$cronFile) return array();
		return $this->_getDao()->getCronByFile($cronFile);
	}
	
	public function fetchCron($cronIds){
		if (!is_array($cronIds) && count($cronIds) <1) return array();
		return $this->_getDao()->fetchCron($cronIds, 'cron_id');
	}
	
	public function getFirstCron() {
		return $this->_getDao()->getFirstCron();
	}
	
	public function getList($isopen = null) {
		if(isset($isopen)) $isopen = (int)$isopen;
		return $this->_getDao()->getList($isopen);
	}
	
	public function addCron(PwCronDm $dm) {
		$resource = $dm->beforeAdd();
		if ($resource instanceof PwError) return $resource;
		return $this->_getDao()->addCron($dm->getData());
	}
	
	public function updateCron(PwCronDm $dm) {
		$resource = $dm->beforeUpdate();
		if ($resource instanceof PwError) return $resource;
		return $this->_getDao()->updateCron($dm->cronId, $dm->getData());
	}
	
	public function updateNextTime($cronId, $nextTime) {
		$nextTime = (int)$nextTime;
		$cronId = (int)$cronId;
		if (!$cronId || !$nextTime) return false;
		$data['next_time'] = $nextTime;
		return $this->_getDao()->updateCron($cronId, $data);
	}
	
	public function deleteCron($cronId) {
		$cronId = (int)$cronId;
		if ($cronId < 1) return false;
		return $this->_getDao()->deleteCron($cronId);
	}
	
	private function _getDao() {
		return Wekit::loadDao('cron.dao.PwCronDao');
	}
}

?>