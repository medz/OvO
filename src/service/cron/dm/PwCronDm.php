<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$ 
 * @package 
 */
class PwCronDm extends PwBaseDm {
	
	public $cronId;
	public function __construct($cronId = null){
		if (isset($cronId))$this->cronId = (int)$cronId;
	}
	
	public function setSubject($subject) {
		$this->_data['subject'] = Pw::substrs($subject, 20);
		return $this;
	}
	
	public function setLooptype($type) {
		$this->_data['loop_type'] = $type;
		return $this;
	}
	public function setLoopdaytime($day = 0, $hour = 0, $minute = 0) {
		$this->_data['loop_daytime'] = $day . '-' . $hour . '-' . $minute;
		return $this;
	}
	
	public function setCronfile($fileName) {
		$this->_data['cron_file'] = $fileName;
		return $this;
	}
	
	public function setIsopen($isopen) {
		$this->_data['isopen'] = (int)$isopen;
		return $this;
	}
	
	public function setCreatedtime($time) {
		$this->_data['created_time'] = (int)$time;
		return $this;
	}
	
	public function setModifiedtime($time) {
		$this->_data['modified_time'] = (int)$time;
		return $this;
	}
	
	public function setNexttime($time) {
		$this->_data['next_time'] = (int)$time;
		return $this;
	}
	
	protected function _beforeAdd() {
		return true;
	}
	
	protected function _beforeUpdate() {
		if (!$this->cronId) return new PwError('fail');
		return true;
	}
}
?>