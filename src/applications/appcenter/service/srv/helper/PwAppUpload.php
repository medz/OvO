<?php
Wind::import('LIB:upload.PwUploadAction');
/**
 * 应用上传
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package appcenter.service.srv.helper
 */
class PwAppUpload extends PwUploadAction {

	//public $ftype = array('zip' => 10000, 'rar' => 10000, 'tar' => 10000, 'tar.gz' => 10000, '7z' => 10000);
	public $ftype = array('zip' => 10000);
	
	/* (non-PHPdoc)
	 * @see PwUploadAction::getSaveName()
	 */
	public function getSaveName(PwUploadFile $file) {
		$filename = substr(md5(Pw::getTime() . $file->id . WindUtility::generateRandStr(8)), 10, 15);
		return $filename . '.' . $file->ext;
	}

	/* (non-PHPdoc)
	 * @see PwUploadAction::getSaveDir()
	 */
	public function getSaveDir(PwUploadFile $file) {
		return 'app/';
	}

	/* (non-PHPdoc)
	 * @see PwUploadAction::update()
	 */
	public function update($uploaddb) {
		return $uploaddb;
	}
}

?>