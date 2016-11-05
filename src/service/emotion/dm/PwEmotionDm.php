<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$ 
 * @package 
 */
class PwEmotionDm extends PwBaseDm {
	
	public $emotionId;
	
	public function __construct($emotionId = null){
		isset($emotionId)&& $this->emotionId = (int)$emotionId;
	}
	
	public function setCategoryId($categoryid) {
		$this->_data['category_id'] = (int)$categoryid;
		return $this;
	}
	
	public function setEmotionName($emotionName) {
		$this->_data['emotion_name'] = Pw::substrs($emotionName, 10);
		return $this;
	}
	
	public function setEmotionFolder($emotionFolder) {
		$this->_data['emotion_folder'] = $emotionFolder;
		return $this;
	}
	
	public function setEmotionIcon($emotionIcon) {
		$this->_data['emotion_icon'] = $emotionIcon;
		return $this;
	}
	
	public function setVieworder($vieworder) {
		$this->_data['vieworder'] = (int)$vieworder;
		return $this;
	}
	
	public function setIsused($isused) {
		$this->_data['isused'] = (int)$isused;
		return $this;
	}
	
	protected function _beforeAdd() {
		return true;
	}
	
	protected function _beforeUpdate() {
		if ($this->emotionId < 1) return new PwError('ADMIN:fail');
		return true;
	}
	
	
}
?>