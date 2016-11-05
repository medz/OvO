<?php
Wind::import('ADMIN:library.AdminBaseController');
/**
 * pw应用后台首页
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 2011-09-22 03:59:17Z yishuo $
 * @package wind
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
			'wind_version' => 'phpwind v' . NEXT_VERSION . ' ' . NEXT_RELEASE,
			'php_version' => PHP_VERSION, 
			'server_software' => str_replace('PHP/' . PHP_VERSION, '', 
				$this->getRequest()->getServer('SERVER_SOFTWARE')), 
			'mysql_version' => $db->getDbHandle()->getAttribute(PDO::ATTR_SERVER_VERSION), 
			'max_upload' => ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'Disabled', 
			'max_excute_time' => intval(ini_get('max_execution_time')) . ' seconds', 
			'sys_mail' => $sysMail);
		$this->setOutput($sysinfo, 'sysinfo');
	}

	/**
	 * 获取升级信息通知
	 */
	public function noticeAction() {
		$notice = Wekit::load('APPCENTER:service.srv.PwSystemInstallation')->getNotice(
			$this->loginUser);
		$this->setOutput($notice, 'html');
		$this->showMessage('success');
	}
}

?>
