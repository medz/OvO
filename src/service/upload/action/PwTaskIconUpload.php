<?php
Wind::import('LIB:upload.PwUploadAction');
Wind::import('COM:utility.WindUtility');

/**
 * 任务图标上传
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskIconUpload.php 23975 2013-01-17 10:20:11Z jieyin $
 * @package upload.action
 */
class PwTaskIconUpload extends PwUploadAction {
	
	private $width;
	private $height;
	private $filename;
	private $dir = 'task/';
	
	/**
	 * 构造方法
	 *
	 * @param int $width
	 * @param int $height
	 */
	public function __construct($width, $height) {
		$this->width = $width;
		$this->height = $height;
		$this->ftype = array('jpg' => 2000, 'png' => '2000', 'jpeg' => 2000);
	}
	
	/* (non-PHPdoc)
	 * @see PwUploadAction::check()
	 */
	public function check() {
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see PwUploadAction::allowType()
	 */
	public function allowType($key) {
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see PwUploadAction::getSaveName()
	 */
	public function getSaveName(PwUploadFile $file) {
		$prename  = substr(md5(Pw::getTime() . WindUtility::generateRandStr(8)), 10, 15);
		$this->filename = $prename . '.' .$file->ext;
		return $this->filename;
	}
	
	/* (non-PHPdoc)
	 * @see PwUploadAction::getSaveDir()
	 */
	public function getSaveDir(PwUploadFile $file) {
		return $this->dir;
	}
	
	/* (non-PHPdoc)
	 * @see PwUploadAction::allowThumb()
	 */
	public function allowThumb() {
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see PwUploadAction::getThumbInfo()
	 */
	public function getThumbInfo($filename, $dir) {
		return array(
			array($this->filename, $this->dir, $this->width, $this->height, 0)
		);
	}
	
	/* (non-PHPdoc)
	 * @see PwUploadAction::allowWaterMark()
	 */
	public function allowWaterMark() {
		return false;
	}

	/* (non-PHPdoc)
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