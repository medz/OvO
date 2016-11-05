<?php
Wind::import('ADMIN:library.AdminBaseController');
/**
 * 后台首页操作处理类
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-14
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.net
 * @version $Id: HomeController.php 22069 2012-12-19 03:46:19Z yishuo $
 * @package admin
 * @subpackage controller
 */
class HomeController extends AdminBaseController {

	/**
	 * 后台首页处理方法
	 */
	public function run() {
		//TODO 后台默认首页内容扩展支持
		if (false != ($sendmail_path = ini_get('sendmail_path'))) {
			$sysMail = 'Unix Sendmail ( Path: ' . $sendmail_path . ')';
		} elseif (false != ($SMTP = ini_get('SMTP'))) {
			$sysMail = 'SMTP ( Server: ' . $SMTP . ')';
		} else {
			$sysMail = 'Disabled';
		}
		$db = Wind::getComponent('db');
		$sysinfo = array(
			'wind_version' => 'phpwind 9.0RC 20121108', 
			'php_version' => PHP_VERSION, 
			'server_software' => str_replace('PHP/' . PHP_VERSION, '', 
				$this->getRequest()->getServer('SERVER_SOFTWARE')), 
			'mysql_version' => $db->getDbHandle()->getAttribute(PDO::ATTR_SERVER_VERSION), 
			'max_upload' => ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'Disabled', 
			'max_excute_time' => intval(ini_get('max_execution_time')) . ' seconds', 
			'sys_mail' => $sysMail);
		$this->setOutput($sysinfo, 'sysinfo');
	}

}

?>