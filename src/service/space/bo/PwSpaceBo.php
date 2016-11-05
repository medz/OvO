<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwSpaceBo.php 25545 2013-03-19 05:51:44Z gao.wanggao $ 
 * @package 
 */
 class PwSpaceBo {
 	public $spaceUid = 0;
 	public $visitUid = 0; 		//访问者UID
 	public $spaceUser; 			//空间User
 	public $space = array(); 	//空间基本信息
 	public $tome = 0;			//访问者与空间的关系
 	
 	const VISITOR = 0; 			//游客
 	const STRANGER = 1;			//陌生人
 	const MYSELF = 2;			//本人
 	const ATTENTION = 3;		//主人关注的
 	const FOLLOWED = 4;			//关注主人的
 	const FRIEND = 5;			//互相关注

 	
 	
 	public function __construct($spaceUid) {
 		$this->spaceUid = (int)$spaceUid;
 		$this->_getSpaceUser();
 		$this->_getSpace();
	}
	
	/**
	 * 设置访问用户
	 * Enter description here ...
	 * @param unknown_type $visitUid
	 */
	public function setVisitUid($visitUid) {
		$this->visitUid = (int)$visitUid;
	}
	
	/**
	 * 设置用户与空间的关系
	 * Enter description here ...
	 */
	public function setTome($spaceUid, $visitUid) {
		$this->tome = $this->_getTome($spaceUid, $visitUid);
	}
	
 	/**
 	 * 判断某个key显示权限
 	 * 
 	 * @param string $key 
 	 */
	public function allowView($key = 'space') {
		if ($this->tome == self::MYSELF) return true;
		if (!isset($this->spaceUser['secret'][$key])) {
			//手机号码默认仅对自己开放
			if ($key == 'mobile') {
				$this->spaceUser['secret'][$key] = self::MYSELF;
			} else {
				$this->spaceUser['secret'][$key] = self::VISITOR;
			}
		}
		switch ($this->spaceUser['secret'][$key]) {
			case 0://完全开放
				return true;
			case 1://对自已开放
				if ($this->tome == self::MYSELF) return true;
				break;
			case 2://对关注的人开放
				if ($this->tome == self::ATTENTION)	return true;
				break;
		}
		return false;
	}
	
 	private function _getSpaceUser() {
		$this->spaceUser = Wekit::load('user.PwUser')->getUserByUid($this->spaceUid, PwUser::FETCH_ALL);
		$this->spaceUser['secret'] = unserialize($this->spaceUser['secret']);
		!isset($this->spaceUser['secret']['mobile']) && $this->spaceUser['secret']['mobile'] = self::MYSELF;
	}
	
	private function _getSpace() {
		$this->space =  $this->_getSpaceDs()->getSpace($this->spaceUid);
		empty($this->space['space_name']) && $this->space['space_name'] = $this->spaceUser['username'] . '的个人空间';
		empty($this->space['space_privacy']) && $this->space['space_privacy'] = array();
		$this->space['domain'] = $this->_getDomain();

		list($image, $repeat, $fixed, $align) =  unserialize($this->space['back_image']);
		empty($repeat) && $repeat = 'no-repeat';
		empty($fixed) && $fixed = 'scroll';
		empty($align) && $align = 'center';
		$this->space['back_image'] = array($image, $repeat, $fixed, $align);
		$this->space['backbround'] = '';
		if (empty($image)) return;
		$image = Pw::getPath(''). $image;
		$this->space['backbround'] = 'style="';
		$this->space['backbround'] .= 'background-image: url( ' . $image . ');';
		$this->space['backbround'] .= 'background-repeat:' . $repeat . ';' ;
		$this->space['backbround'] .= 'background-attachment:' .$fixed . ';' ;
		$this->space['backbround'] .= 'background-position:top ' . $align . ';';
		$this->space['backbround'] .= '"';
		return;
	}
	
 	private function _getDomain() {
 		if ($this->space['space_domain']) {
 			$root = Wekit::C('domain', 'space.root');
 			if ($root) return 'http://' . $this->space['space_domain'] . '.' . $root;
 		}
 		return WindUrlHelper::createUrl('space/index/run', array('uid' => $this->spaceUid));
 	}
	
	/**
	 * 获取访问者和空间的关系
	 * 0未登录,1未关注,2本人,3主人关注的, 4,关注主人的 5互相关注
	 */
 	private function _getTome($spaceUid, $visitUid) {
 		$attention = $followed = false;
 		if ($visitUid == 0) return self::VISITOR;  
 		if ($visitUid == $spaceUid) return self::MYSELF; 
 		if (Wekit::load('attention.PwAttention')->isFollowed($spaceUid, $visitUid)) {
 			$attention = true; //self::ATTENTION; 
 		}
 		if (Wekit::load('attention.PwAttention')->isFollowed($visitUid, $spaceUid)) {
 			$followed = true; //self::FOLLOWED; 
 		}
 		if ($attention && $followed) return self::FRIEND;
 		if ($attention) return self::ATTENTION;
 		if ($followed) return self::FOLLOWED;
 		return self::STRANGER;
 	}
 	
 	private function _getSpaceDs() {
 		return Wekit::load('space.PwSpace');
 	}
 }
?>
