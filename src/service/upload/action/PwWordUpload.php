<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:upload.PwUploadAction');


class PwWordUpload extends PwUploadAction {

	public $isLocal = true;

	public $ftype = array('txt' => 10000);
	public $dir = 'pw/word/';
	public $filename;
	
	public function getSaveName(PwUploadFile $file) {
		$filename = substr(md5(Pw::getTime() . WindUtility::generateRandStr(8)), 10, 15);
		$this->filename = $filename . '.' . $file->ext;
		return $this->filename;
	}

	public function getSaveDir(PwUploadFile $file) {
		return  $this->dir;
	}

	public function update($uploaddb) {
		$this->attachs = $uploaddb[0];
		return true;
	}

	public function getAbsoluteFile() {
		return Wind::getRealDir('PUBLIC:') . PUBLIC_ATTACH . '/'.$this->dir.$this->filename;
	}
}
?>