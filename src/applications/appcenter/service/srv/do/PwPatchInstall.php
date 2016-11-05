<?php
Wind::import('APPCENTER:service.srv.iPwInstall');
/**
 * 安装补丁
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwPatchInstall.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package appcenter
 */
class PwPatchInstall implements iPwInstall {
	
	protected $fileList = array();
	
	/*
	 * (non-PHPdoc) @see iPwInstall::install()
	 */
	public function install($install) {
		$tmp = $install->getTmpPackage();
		$fileList = PwApplicationHelper::readRecursive($tmp);
		if (1 > count($fileList)) return true;
		$this->fileList = $fileList;
		return true;
	}

	/**
	 *
	 * @param PwInstallApplication $install        	
	 */
	public function afterInstall($install) {
		$tmp = $install->getTmpPackage();
		WindFile::del($tmp . '/Manifest.xml');
		PwApplicationHelper::copyRecursive($tmp, ROOT_PATH);
	}
	
	/*
	 * (non-PHPdoc) @see iPwInstall::backUp()
	 */
	public function backUp($install) {
		return true;
	}
	
	/*
	 * (non-PHPdoc) @see iPwInstall::revert()
	 */
	public function revert($install) {
		return true;
	}
	
	/*
	 * (non-PHPdoc) @see iPwInstall::unInstall()
	 */
	public function unInstall($install) {
		return true;
	}
	
	/*
	 * (non-PHPdoc) @see iPwInstall::rollback()
	 */
	public function rollback($install) {
		return true;
	}
}

?>