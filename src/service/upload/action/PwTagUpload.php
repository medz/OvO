<?php
Wind::import('LIB:upload.PwUploadAction');
Wind::import('COM:utility.WindUtility');

/**
 * 话题图标上传
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwTagUpload.php 23975 2013-01-17 10:20:11Z jieyin $
 * @package wind
 */
class PwTagUpload extends PwUploadAction {
	
	private $width;
	private $height;
	private $filename;
	private $dir = 'tag/';
	
	/**
	 * 构造方法
	 *
	 * @param int $width
	 * @param int $height
	 */
	public function __construct($width = null, $height = null) {
		$width && $this->width = $width;
		$height && $this->height = $height;
		$this->ftype = array('jpg' => 2000, 'png' => '2000', 'gif' => 2000, 'bmp' => 2000, 'jpeg' => 2000);
	}
	
	/**
	 * @see PwUploadAction.check
	 */
	public function check() {
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
		$prename  = substr(md5(Pw::getTime() . WindUtility::generateRandStr(8)), 10, 15);
		$this->filename = $prename . '.' .$file->ext;
		return $this->filename;
	}
	
	/**
	 * @see PwUploadAction::getSaveDir()
	 */
	public function getSaveDir(PwUploadFile $file) {
		return $this->dir;
	}
	
	/**
	 * @see PwUploadAction::allowThumb()
	 */
	public function allowThumb() {
		return false;
	}
	
	/**
	 * @see PwUploadAction::getThumbInfo()
	 */
	public function getThumbInfo($filename, $dir) {
		return array(
			array($this->filename, $this->dir, $this->width, $this->height, 1)
		);
	}
	
	/**
	 * @see PwUploadAction::allowWaterMark()
	 */
	public function allowWaterMark() {
		return false;
	}

	/**
	 * @see PwUploadAction::update()
	 */
	public function update($uploaddb) {
		$this->attachs = $uploaddb[0];
		$this->path = $this->dir. $this->filename;
		return true;
	}

	/**
	 * 获得上传文件保存的路径
	 *
	 * @return string
	 */
	public function getPath() {
		return $this->filename ? $this->dir . $this->filename : '';
	}
}
?>