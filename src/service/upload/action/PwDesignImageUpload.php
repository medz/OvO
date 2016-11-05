<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:upload.PwUploadAction');
Wind::import('COM:utility.WindUtility');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignImageUpload.php 28882 2013-05-28 10:51:23Z gao.wanggao $ 
 * @package 
 */

class PwDesignImageUpload extends PwUploadAction {
	
	private $moduleid;
	private $mime = array();
	
	public function __construct($moduleid = 0) {
		$this->moduleid = $moduleid;
		$this->ftype = array('jpeg' => 2000,'jpg' => 2000, 'png' => 2000, 'gif' => 2000);
		$this->mime  = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
	}
	
	/**
	 * @see PwUploadAction.check
	 */
	public function check() {
		if (!$_FILES['upload']['size']) return new PwError('upload.fail');
		return true;
	}
	
	/**
	 * @see PwUploadAction.allowType
	 */
	public function allowType($key) {
		return true;
	}
	
	/**
	 * @see PwUploadAction.getSaveName
	 */
	public function getSaveName(PwUploadFile $file) {
		$prename  = substr(md5(rand(111,999)), 10, 15);
		$this->filename = $prename . '.' .$file->ext;
		return $this->filename;
	}
	
	/**
	 * @see PwUploadAction.getSaveDir
	 */
	public function getSaveDir(PwUploadFile $file) {
		return  $this->dir = 'module/'.$this->moduleid.'/';
	}
	
	/**
	 * @see PwUploadAction.allowThumb
	 */
	public function allowThumb() {
		return false;
	}
	
	/**
	 * @see PwUploadAction.getThumbInfo
	 */
	public function getThumbInfo($filename, $dir) {
		return array();
	}
	
	/**
	 * @see PwUploadAction.allowWaterMark
	 */
	public function allowWaterMark() {
		return false;
	}
	
	public function transfer() {
		return false;
	}

	/**
	 * @see PwUploadAction.update
	 */
	public function update($uploaddb) {
		foreach ($uploaddb as $key => $value) {
			$this->attachs = array(
				'name'      => $value['name'],
				'type'      => $value['type'],
				'path'		=> $this->dir,
				'filename'	=> $this->filename,
				'size'      => $value['size'],
				'ext'		=> $value['ext'],
			);
		}
		return true;
	}

	public function getAttachInfo() {
		return $this->attachs;
	}
}
?>