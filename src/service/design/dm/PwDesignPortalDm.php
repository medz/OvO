<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignPortalDm.php 17219 2012-09-04 06:05:24Z gao.wanggao $ 
 * @package 
 */

class PwDesignPortalDm extends PwBaseDm {
	public $id;

	public function __construct($id = null) {
		if (isset($id))$this->id = (int)$id;
	}
	
	public function setPageName($name) {
		$this->_data['pagename'] = Pw::substrs($name, 20);
		return $this;
	}
	
	public function setTitle($title) {
		$this->_data['title'] = Pw::substrs($title, 120);
		return $this;
	}
	
	public function setDescription($description) {
		$this->_data['description'] = Pw::substrs($description, 120);
		return $this;
	}
	
	
	public function setKeywords($keywords) {
		$this->_data['keywords'] = Pw::substrs($keywords, 120);
		return $this;
	}
	
	
	public function setDomain($domain) {
		$this->_data['domain'] = Pw::substrs($domain, 20);
		return $this;
	}
	
	public function setCover($cover) {
		$this->_data['cover'] = $cover;
		return $this;
	}
	
	public function setIsopen($isopen) {
		$this->_data['isopen'] = (int)$isopen;
		return $this;
	}
	
	public function setHeader($header) {
		$this->_data['header'] = (int)$header;
		return $this;
	}
	
	public function setNavigate($navigate) {
		$this->_data['navigate'] = (int)$navigate;
		return $this;
	}
	
	public function setFooter($footer) {
		$this->_data['footer'] = (int)$footer;
		return $this;
	}
	
	public function setTemplate($template) {
		$this->_data['template'] = $template;
		return $this;
	}
	
	public function setStyle($array) {
		$this->_data['style'] = serialize($array);
		return $this;
	}
	
	public function setCreatedUid($uid) {
		$this->_data['created_uid'] = (int)$uid;
		return $this;
	}
	
	public function setCreatedTime($time) {
		$this->_data['created_time'] = (int)$time;
		return $this;
	}
	
	protected function _beforeAdd() {
		//if (!$this->_data['pagename'] || !$this->_data['title']) return new PwError('operate.fail');
		return true;
	}
	
	protected function _beforeUpdate() {
		//if (!$this->_data['pagename'] || !$this->_data['title']) return new PwError('operate.fail');
		if ($this->id < 1) return new PwError('operate.fail');
		return true;
	}
}
?>