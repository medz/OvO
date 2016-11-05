<?php
/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com/license.php
 * @version $Id: iPwInstall.php 19309 2012-10-12 09:03:36Z long.shi $
 * @package wind
 */
interface iPwInstall {

	/**
	 * 应用安装处理过程
	 * 
	 * @param PwInstallApplication $install
	 * @return PwError|true        	
	 */
	public function install($install);
	
	/**
	 * 应用升级中备份过程
	 * 
	 * 记录日志备份，然后删除之前的安装数据
	 *
	 * @param PwUpgradeApplication $install
	 * @return PwError|true
	 */
	public function backUp($install);
	
	/**
	 * 应用升级中恢复备份过程
	 * 
	 * 读取备份日志，恢复升级前状态
	 *
	 * @param PwUpgradeApplication $install
	 * @return PwError|true
	 */
	public function revert($install);

	/**
	 * 应用安装卸载过程
	 *
	 * @param PwUninstallApplication $install
	 * @return PwError|true   
	 */
	public function unInstall($install);

	/**
	 * 当有错误发生时回滚应用安装处理过程
	 *
	 * @param string $install
	 * @return PwError|true 
	 */
	public function rollback($install);
}

?>