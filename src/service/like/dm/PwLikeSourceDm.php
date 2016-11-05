<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$ 
 * @package 
 */
class PwLikeSourceDm extends PwBaseDm {
	public $sid;
	
	public function __construct($sid = null){
		if (isset($sid))$this->sid = (int)$sid;
	}
	
	public function setSubject($subject) {
		$this->_data['subject'] = Pw::substrs($subject, 50);
		return $this;
	}
	
	public function setSourceUrl($sourceUrl) {
		$this->_data['source_url'] = $sourceUrl;
		return $this;
	}
	
	public function setFromApp($fromApp) {
		$this->_data['from_app'] = $fromApp;
		return $this;
	}
	
	public function setFromid($fromid) {
		$this->_data['fromid'] = (int)$fromid;
		return $this;
	}
	
	public function setLikeCount($count) {
		$this->_data['like_count'] = intval($count);
		return $this;
	}
	
	protected function _beforeAdd() {
		return true;
	}
	
	protected function _beforeUpdate() {
		if ($this->sid < 1) {
			return new PwError('BBS:like.fail');
		}
		return true;
	}
	
	
}
?>