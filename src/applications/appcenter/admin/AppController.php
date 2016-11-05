<?php
Wind::import('ADMIN:library.AdminBaseController');
Wind::import('APPCENTER:service.srv.helper.PwApplicationHelper');
Wind::import('APPCENTER:service.srv.helper.PwManifest');
/**
 * 后台 - 我的应用
 *
 * @author Zhu Dong <zhudong0808@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: AppController.php 28922 2013-05-30 08:02:34Z long.shi $
 * @package appcenter.admin
 */
class AppController extends AdminBaseController {
	private $perpage = 10;

	/**
	 * 应用已安装
	 *
	 * @see WindController::run()
	 */
	public function run() {
		$page = (int) $this->getInput('page');
		$page < 1 && $page = 1;
		
		$count = (int) $this->_appDs()->count();
		$_page = ceil($count / $this->perpage);
		$page > $_page && $page = $_page;
		list($start, $num) = Pw::page2limit($page, $this->perpage);
		$apps = $this->_appDs()->fetchByPage($num, $start);
		$this->setOutput(
			array('perpage' => $this->perpage, 'page' => $page, 'count' => $count, 'apps' => $apps));
	}

	/**
	 * 获取应用更新信息及卸载信息
	 */
	public function refreshAction() {
		$app_ids = $this->getInput('app_ids');
		$apps = $data = array();
		$url = PwApplicationHelper::acloudUrl(
			array('a' => 'forward', 'do' => 'fetchApp', 'appids' => $app_ids));
		$result = PwApplicationHelper::requestAcloudData($url);
		$result['code'] === '0' && $apps = $result['info'];
		foreach (explode(',', $app_ids) as $v) {
			$data[$v] = array(
				'update_url' => '', 
				'admin_url' => trim($apps[$v]['admin_url'], '\'"'), 
				'update_url' => $apps[$v]['update'] ? 1 : 0, 
				'open_new' => $apps[$v]['open_new'] ? 1 : 0);
		}
		$this->setOutput($data, 'data');
		$this->showMessage('success');
	}

	/**
	 * 本地安装 - 上传
	 */
	public function uploadAction() {
		$authkey = 'AdminUser';
		$pre = Wekit::C('site', 'cookie.pre');
		$pre && $authkey = $pre . '_' . $authkey;
		$winduser = $this->getInput($authkey, 'post');
		if (!$winduser) $this->showError('login.not');
		list($type, $u, $pwd) = explode("\t", Pw::decrypt(urldecode($winduser)));
		if ($type == 'founder') {
			$founders = Wekit::load('ADMIN:service.srv.AdminFounderService')->getFounders();
			if (!isset($founders[$u])) $this->showError('login.not');
			list($md5pwd, $salt) = explode('|', $founders[$u], 2);
			if (Pw::getPwdCode($md5pwd) != $pwd) $this->showError('login.not');
		} else {
			$r = Wekit::load('user.PwUser')->getUserByUid($u);
			if (!$r) $this->showError('login.not');
			if (Pw::getPwdCode($r['password']) != $pwd) $this->showError('login.not');
		}
		
		Wind::import('SRC:applications.appcenter.service.srv.helper.PwApplicationUpload');
		$upload = new PwApplicationUpload();
		$upload->dir = Wind::getRealDir($this->_installService()->getConfig('tmp_dir'), true) . '/';
		$uploaddb = $upload->execute();
		if ($uploaddb instanceof PwError) $this->showError($uploaddb->getError());
		if (empty($uploaddb)) $this->showError('upload.fail');
		$this->setOutput(
			array('filename' => $uploaddb[0]['name'], 'file' => $uploaddb[0]['fileuploadurl']), 
			'data');
		$this->showMessage('success');
	}

	/**
	 * 本地安装, 打印本地安装页面
	 */
	public function installAction() {
		$ext = Wind::getRealDir('EXT:', true);
		$dirs = WindFolder::read($ext, WindFolder::READ_DIR);
		$manifests = array();
		$result = array_keys($this->_appDs()->fetchByAlias($dirs, 'alias'));
		$temp = array_diff($dirs, $result);
		$to_install = array();
		foreach ($temp as $v) {
			if (file_exists($ext . $v . '/Manifest.xml')) $to_install[] = $v;
		}
		$this->setOutput($to_install, 'apps');
	}
	
	/**
	 * 目录扫描安装
	 *
	 */
	public function toInstallAction() {
		$apps = $this->getInput('apps', 'get');
		$ext = Wind::getRealDir('EXT:', true);
		$srv = Wekit::load('APPCENTER:service.srv.PwDebugApplication');
		foreach ($apps as $v) {
			$r = $srv->installPack($ext . $v);
			if ($r instanceof PwError) $this->showError($r->getError());
		}
		$this->showMessage('success', 'appcenter/app/install', true);
	}

	/**
	 * 本地安装, 分步模式执行应用安装
	 */
	public function doInstallAction() {
		list($file, $step, $hash) = $this->getInput(array('file', 'step', 'hash'));
		$install = $this->_installService();
		if ($file) {
			$file = Wind::getRealDir($install->getConfig('tmp_dir'), true) . '/' . $file;
			$install->setTmpPath(dirname($file));
			if (!WindFile::isFile($file)) $this->showError('APPCENTER:install.checkpackage.fail');
			$_r = $install->extractPackage($file);
			if (true !== $_r) $this->showError('APPCENTER:install.checkpackage.format.fail');
			$this->addMessage('APPCENTER:install.step.express');
			$_r = $install->initInstall();
			if (true !== $_r) $this->showError('APPCENTER:install.initinstall.fail');
			$this->addMessage('APPCENTER:install.step.init');
			$hash = $install->getHash();
			$this->addMessage('APPCENTER:install.step.install');
		}
		
		$step || $step = 0;
		//$_r = $install->doInstall($step, $hash);
		//在360和ie下，写日志有问题，而且多web环境下，多进程安装也是有问题的
		$_r = $install->doInstall('all', $hash);
		if (true === $_r) {
			$install->clear();
			$this->showMessage('APPCENTER:install.success');
		} elseif (is_array($_r)) {
			$this->setOutput(array('step' => $_r[0], 'hash' => $hash), 'data');
			$this->showMessage($_r[1]);
		} else {
			$install->rollback();
			$this->addMessage(array('step' => $step, 'hash' => $hash), 'data');
			$this->showError($_r->getError());
		}
	}

	/**
	 * 测试升级流程
	 */
	public function testUpgradeAction() {
		list($file) = $this->getInput(array('file'));
		/* @var $install PwUpgradeApplication */
		$install = Wekit::load('APPCENTER:service.srv.PwUpgradeApplication');
		$install->_appId = 'L0001344318635mEhO';
		$file = Wind::getRealDir($install->getConfig('tmp_dir'), true) . '/' . $file;
		$install->setTmpPath(dirname($file));
		$install->extractPackage($file);
		$install->initInstall();
		$_r = $install->doUpgrade();
		if (true === $_r) {
			$install->clear();
			$this->showMessage('APPCENTER:install.success');
		} else {
			$install->rollback();
			$this->showError($_r->getError());
		}
	}

	/**
	 * 删除已上传压缩包
	 */
	public function delFileAction() {
		$file = $this->getInput('file', 'post');
		if ($file && file_exists(ATTACH_PATH . $file)) {
			WindFile::del(ATTACH_PATH . $file);
		}
		$this->showMessage('success');
	}
	
	/**
	 * 删除应用目录
	 *
	 */
	public function delFolderAction() {
		$folder = $this->getInput('folder', 'post');
		if ($folder) {
			is_dir(EXT_PATH . $folder) && WindFolder::clearRecur(EXT_PATH . $folder, true);
			is_dir(THEMES_PATH . 'extres/' . $folder) && WindFolder::clearRecur(THEMES_PATH . 'extres/' . $folder, true);
		}
		$this->showMessage('success');
	}

	/**
	 * 应用搜索
	 */
	public function searchAction() {
		$keyword = $this->getInput('keyword', 'post');
		$apps = array();
		$count = $this->_appDs()->countSearchByName($keyword);
		if ($count > 0) {
			$page = intval($this->getInput('page'));
			$total = ceil($count / $this->perpage);
			$page < 1 && $page = 1;
			$page > $total && $page = $total;
			list($start, $num) = Pw::page2limit($page, $this->perpage);
			$apps = $this->_appDs()->searchByName($keyword, $num, $start);
		}
		$this->setOutput(
			array(
				'perpage' => $this->perpage, 
				'page' => $page, 
				'count' => $count, 
				'apps' => $apps, 
				'keyword' => $keyword, 
				'search' => 1));
		$this->setTemplate('app_run');
	}

	/**
	 * 获取扩展信息
	 */
	public function hookAction() {
		$alias = $this->getInput('alias');
		$manifest = Wind::getRealPath('EXT:' . $alias . '.Manifest.xml', true);
		$hooks = $injectors = array();
		if (is_file($manifest)) {
			$man = new PwManifest($manifest);
			$hooks = $man->getHooks();
			$injectors = $man->getInjectServices();
		}
		$this->setOutput(array('hooks' => $hooks, 'injectors' => $injectors));
	}

	/**
	 * 卸载
	 */
	public function uninstallAction() {
		$id = $this->getInput('app_id');
		/* @var $uninstall PwUninstallApplication */
		if ($id[0] !== 'L') {
			$url = PwApplicationHelper::acloudUrl(
				array('a' => 'forward', 'do' => 'uninstallApp', 'appid' => $id));
			$info = PwApplicationHelper::requestAcloudData($url);
			if ($info['code'] !== '0')
				$this->showError($info['msg']);
			else
				$this->showMessage('success');
		} else {
			$uninstall = Wekit::load('APPCENTER:service.srv.PwUninstallApplication');
			$r = $uninstall->uninstall($id);
			if ($r === true) $this->showMessage('success');
			$this->showError($r->getError());
		}
	}
	
	public function scanAction() {
		$ext = Wind::getRealDir('EXT:', true);
		$dirs = WindFolder::read($ext, WindFolder::READ_DIR);
		$alias = array();
		foreach ($dirs as $file) {
			if (WindFile::isFile($ext . '/' . $file . '/Manifest.xml')) $alias[] = $file;
		}
		$result = $this->_appDs()->fetchByAlias($alias, 'alias');
		$to_install = array_diff($alias, array_keys($result));
		if (!$to_install) $this->showMessage('success');
		
	}
	
	/**
	 * 升级
	 */
	public function upgradeAction() {
		$id = $this->getInput('app_id');
		$url = PwApplicationHelper::acloudUrl(
			array('a' => 'forward', 'do' => 'upgradeApplication', 'appid' => $id));
		$info = PwApplicationHelper::requestAcloudData($url);
		if ($info['code'] !== '0')
			$this->showError(array('APPCENTER:update.fail', array($info['msg'])));
		else
			$this->showMessage('success');
	}
	
	/**
	 * 导出压缩包
	 *
	 */
	public function exportAction() {
		$alias = $this->getInput('alias', 'get');
		Wind::import('LIB:utility.PwZip');
		$dir = Wind::getRealDir('EXT:' . $alias);
		if (!is_dir($dir)) $this->showError('fail');
		$target = Wind::getRealPath('DATA:tmp.' . $alias . '.zip', true);
		PwApplicationHelper::zip($dir, $target);
		$timestamp = Pw::getTime();
		$this->getResponse()->setHeader('Last-Modified', gmdate('D, d M Y H:i:s', $timestamp + 86400) . ' GMT');
		$this->getResponse()->setHeader('Expires', gmdate('D, d M Y H:i:s', $timestamp + 86400) . ' GMT');
		$this->getResponse()->setHeader('Cache-control', 'max-age=86400');
		$this->getResponse()->setHeader('Content-type', 'application/x-zip-compressed');
		$this->getResponse()->setHeader('Content-Disposition', 'attachment; filename=' . $alias . '.zip');
		$this->getResponse()->sendHeaders();
		@readfile($target);
		WindFile::del($target);
		$this->getResponse()->sendBody();
		exit();
	}

	/**
	 *
	 * @return PwApplication
	 */
	private function _appDs() {
		return Wekit::load('APPCENTER:service.PwApplication');
	}

	/**
	 *
	 * @return PwInstallApplication
	 */
	private function _installService() {
		return Wekit::load('APPCENTER:service.srv.PwInstallApplication');
	}
	
}

?>