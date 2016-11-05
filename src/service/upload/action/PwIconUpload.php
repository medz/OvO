<?php
Wind::import('LIB:upload.PwUploadAction');
class PwIconUpload extends PwUploadAction {
	
	public $key;
	public $dir;
	
	public function __construct($key, $dir = '') {
		$this->key = $key;
		$this->dir = $dir;
		$this->ftype = array('jpg' => 2000, 'png' => '2000', 'gif' => 2000);
	}
	/* (non-PHPdoc)
	 * @see PwUploadAction::getSaveName()
	 */
	public function getSaveName(PwUploadFile $file) {
		$prename  = substr(md5(Pw::getTime() . WindUtility::generateRandStr(8)), 10, 15);
		$this->filename = $prename . '.' .$file->ext;
		return $this->filename;
	}
	
	public function allowType($key) {
		if ($this->key && $key != $this->key) return false;
		return true;
	}

	/* (non-PHPdoc)
	 * @see PwUploadAction::getSaveDir()
	 */
	 public function getSaveDir(PwUploadFile $file) {
		return $this->dir;
	}

	/* (non-PHPdoc)
	 * @see PwUploadAction::update()
	 */
	 public function update($uploaddb) {
		foreach ($uploaddb as $key => $value) {
			$this->attachs = array(
				'attname'      => $value['attname'],
				'type'      => $value['type'],
				'path'		=> $value['fileuploadurl'],
				'size'      => $value['size'],
				'width'		=> $this->width,
				'height'	=> $this->height
			);
		}
		return true;
	}
	
	public function getAttachInfo() {
		return $this->attachs;
	}
}

?>