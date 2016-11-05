<?php
Wind::import('ADMIN:library.AdminBaseController');
Wind::import('APPCENTER:service.srv.helper.PwApplicationHelper');
/**
 * 后台 - 我的模板
 *
 * @author Zhu Dong <zhudong0808@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: StyleController.php 24598 2013-02-01 06:44:48Z long.shi $
 * @package appcenter.admin
 */
class StyleController extends AdminBaseController {
	private $perpage = 10;

	/**
	 * 整站模板
	 *
	 * @see WindController::run()
	 */
	public function run() {
		$type = $this->getInput('type');
		$addons = Wekit::load('APPCENTER:service.srv.PwInstallApplication')->getConfig(
			'style-type');
		$type || $type = key($addons);
		
		$count = $this->_styleDs()->countByType($type);
		$results = array();
		if ($count > 0) {
			$page = (int) $this->getInput('page');
			$page < 1 && $page = 1;
			list($start, $num) = Pw::page2limit($page, $this->perpage);
			$results = $this->_styleDs()->getStyleListByType($type, $num, $start);
		}
		$this->setOutput(
			array(
				'type' => $type, 
				'addons' => $addons, 
				'perpage' => $this->perpage, 
				'page' => $page, 
				'count' => $count, 
				'styles' => $results));
	}

	/**
	 * 界面管理
	 */
	public function manageAction() {
		$addons = Wekit::load('APPCENTER:service.srv.PwInstallApplication')->getConfig(
			'style-type');
		$this->setOutput($addons, 'addons');
		
		$conf = Wekit::C('css');
		$this->setOutput(
			array(
				'logo' => $conf['logo'], 
				'bg' => $conf['bg'], 
				'bgcolor' => $conf['bgcolor'], 
				'bgtile' => $conf['bgtile'], 
				'bgalign' => $conf['bgalign'], 
				'size' => $conf['size'], 
				'font' => $conf['font'], 
				'corelink' => $conf['corelink'], 
				'coretext' => $conf['coretext'], 
				'subjectsize' => $conf['subjectsize'], 
				'contentsize' => $conf['contentsize'],
				'headbg' => $conf['headbg'], 
				'headbgcolor' => $conf['headbgcolor'], 
				'headbgtile' => $conf['headbgtile'], 
				'headbgalign' => $conf['headbgalign'], 
				'headlink' => $conf['headlink'], 
				'headactivelink' => $conf['headactivelink'], 
				'headactivecolor' => $conf['headactivecolor'], 
				'boxbg' => $conf['boxbg'], 
				'boxbgcolor' => $conf['boxbgcolor'], 
				'boxbgtile' => $conf['boxbgtile'], 
				'boxbgalign' => $conf['boxbgalign'], 
				'boxborder' => $conf['boxborder'], 
				'boxlink' => $conf['boxlink'], 
				'boxtext' => $conf['boxtext'], 
				'boxhdbg' => $conf['boxhdbg'], 
				'boxhdbgcolor' => $conf['boxhdbgcolor'], 
				'boxhdbgtile' => $conf['boxhdbgtile'], 
				'boxhdbgalign' => $conf['boxhdbgalign'], 
				'boxhdborder' => $conf['boxhdborder'], 
				'boxhdlink' => $conf['boxhdlink'], 
				'boxhdtext' => $conf['boxhdtext']));
	}

	/**
	 * 界面管理
	 */
	public function doManageAction() {
		$config = Wekit::C('css');
		$logo = $this->_upload('logo');
		if ($logo) {
			$config['logo'] = $logo['path'];
			$old = $this->getInput('oldlogo');
			$old && Pw::deleteAttach($old);
		}
		$bg = $this->_upload('bg');
		if ($bg) {
			$config['bg'] = $bg['path'];
			$old = $this->getInput('oldbg');
			$old && Pw::deleteAttach($old);
		}
		$headbg = $this->_upload('headbg');
		if ($headbg) {
			$config['headbg'] = $headbg['path'];
			$old = $this->getInput('oldheadbg');
			$old && Pw::deleteAttach($old);
		}
		$boxbg = $this->_upload('boxbg');
		if ($boxbg) {
			$config['boxbg'] = $boxbg['path'];
			$old = $this->getInput('oldboxbg');
			$old && Pw::deleteAttach($old);
		}
		$boxhdbg = $this->_upload('boxhdbg');
		if ($boxhdbg) {
			$config['boxhdbg'] = $boxhdbg['path'];
			$old = $this->getInput('oldboxhdbg');
			$old && Pw::deleteAttach($old);
		}
		list($color, $headbgcolor, $headlink, $headactivelink, $headactivecolor, $corelink, $coretext, $boxbgcolor, $boxborder, $boxlink, $boxtext, $boxhdbgcolor, $boxhdborder, $boxhdlink, $boxhdtext) = $this->getInput(
			array(
				'bgcolor', 
				'headbgcolor', 
				'headlink', 
				'headactivelink', 
				'headactivecolor', 
				'corelink', 
				'coretext', 
				'boxbgcolor', 
				'boxborder', 
				'boxlink', 
				'boxtext', 
				'boxhdbgcolor', 
				'boxhdborder', 
				'boxhdlink', 
				'boxhdtext'), 'post');
		$config = array(
			'bgcolor' => $color == '#ffffff' ? '' : $color, 
			'headbgcolor' => $headbgcolor == '#ffffff' ? '' : $headbgcolor, 
			'headlink' => $headlink == '#ffffff' ? '' : $headlink, 
			'headactivelink' => $headactivelink == '#ffffff' ? '' : $headactivelink, 
			'corelink' => $corelink == '#ffffff' ? '' : $corelink, 
			'coretext' => $coretext == '#ffffff' ? '' : $coretext, 
			'headactivecolor' => $headactivecolor == '#ffffff' ? '' : $headactivecolor, 
			'boxbgcolor' => $boxbgcolor == '#ffffff' ? '' : $boxbgcolor, 
			'boxborder' => $boxborder == '#ffffff' ? '' : $boxborder, 
			'boxlink' => $boxlink == '#ffffff' ? '' : $boxlink, 
			'boxtext' => $boxtext == '#ffffff' ? '' : $boxtext, 
			'boxhdbgcolor' => $boxhdbgcolor == '#ffffff' ? '' : $boxhdbgcolor, 
			'boxhdborder' => $boxhdborder == '#ffffff' ? '' : $boxhdborder, 
			'boxhdlink' => $boxhdlink == '#ffffff' ? '' : $boxhdlink, 
			'boxhdtext' => $boxhdtext == '#ffffff' ? '' : $boxhdtext, 
			'bgtile' => $this->getInput('bgtile', 'post'), 
			'bgalign' => $this->getInput('bgalign', 'post'), 
			'size' => $this->getInput('size', 'post'), 
			'font' => $this->getInput('font', 'post'), 
			'subjectsize' => $this->getInput('subjectsize', 'post'), 
			'contentsize' => $this->getInput('contentsize', 'post'), 
			'headbgtile' => $this->getInput('headbgtile', 'post'), 
			'headbgalign' => $this->getInput('headbgalign', 'post'), 
			'boxbgtile' => $this->getInput('boxbgtile', 'post'), 
			'boxbgalign' => $this->getInput('boxbgalign', 'post'), 
			'boxhdbgtile' => $this->getInput('boxhdbgtile', 'post'), 
			'boxhdbgalign' => $this->getInput('boxhdbgalign', 'post')) + $config;
		$bo = new PwConfigSet('css');
		foreach ($config as $k => $v) {
			$bo->set($k, $v);
		}
		$bo->flush();
		$this->_compilerService()->doCompile($config);
		$this->showMessage('success');
	}

	/**
	 * 删除图标，logo
	 */
	public function deleteAction() {
		list($type, $path) = $this->getInput(array('type', 'path'));
		Pw::deleteAttach($path);
		Wekit::C()->setConfig('css', $type, '');
		$this->_compilerService()->doCompile();
		$this->showMessage('success');
	}
	
	/**
	 * 删除应用目录
	 *
	 */
	public function delFolderAction() {
		$folder = $this->getInput('folder');
		$dir = Wind::getRealDir('THEMES:' . $folder);
		WindFolder::clearRecur($dir, true);
		$this->showMessage('success');
	}

	/**
	 * 设为默认
	 */
	public function defaultAction() {
		$styleid = $this->getInput("styleid");
		if (($result = $this->_styleService()->useStyle($styleid)) instanceof PwError) $this->showError(
			$result->getError());
		$this->showMessage('success');
	}

	/**
	 * 风格 - 扫描未安装的风格
	 */
	public function installAction() {
		$addons = Wekit::load('APPCENTER:service.srv.PwInstallApplication')->getConfig(
			'style-type');
		$themes = $this->_styleService()->getUnInstalledThemes();
		$this->setOutput($themes, 'themes');
		$this->setOutput($addons, 'addons');
	}
	
	/**
	 * 导出压缩包
	 *
	 */
	public function exportAction() {
		list($type, $alias) = $this->getInput(array('type', 'alias'), 'get');
		$conf = Wekit::load('APPCENTER:service.srv.PwInstallApplication')->getConfig(
			'style-type', $type);
		if (!$conf) $this->showMessage('fail');
		Wind::import('LIB:utility.PwZip');
		$dir = Wind::getRealDir('THEMES:') . DIRECTORY_SEPARATOR . $conf[1] . DIRECTORY_SEPARATOR . $alias;
		if (!is_dir($dir)) $this->showError('fail');
		$target = Wind::getRealPath('DATA:tmp.' . $alias . '.zip', true);
		PwApplicationHelper::zip($dir, $target);
		$this->getResponse()->setHeader('Content-type', 'application/x-zip-compressed');
		$this->getResponse()->setHeader('Content-Disposition', 'attachment; filename=' . $alias . '.zip');
		$this->getResponse()->setHeader('Expires', '0');
		$this->getResponse()->sendHeaders();
		readfile($target);
		WindFile::del($target);
		$this->getResponse()->sendBody();
		exit();
	}

	/**
	 * 风格预览
	 */
	public function previewAction() {
		$id = $this->getInput("styleid");
		$addons = Wekit::load('APPCENTER:service.srv.PwInstallApplication')->getConfig(
			'style-type');
		$style = $this->_styleDs()->getStyle($id);
		Pw::setCookie('style_preview', $style['alias'] . '|' . $style['style_type'], 20);
		$url = $addons[$style['style_type']][2];
		if ($style['style_type'] == 'space') {
			$url .= '?username=' . $this->loginUser->username;
		} /* else if ($style['style_type'] == 'forum') {
			$forums = Wekit::load('forum.PwForum')->getForumOrderByType(false);
			$url .= '?fid=' . key($forums);
		} */
		$this->forwardRedirect(
			WindUrlHelper::createUrl($url, array(), '', 'pw'));
	}

	/**
	 * 安装未安装的风格列表
	 */
	public function doInstallAction() {
		$themes = $this->getInput('themes');
		if (!$themes) $this->showError('STYLE:style.illegal.themes', 'appcenter/style/install');
		
		foreach ($themes as $theme) {
			if (($result = $this->_install(Wind::getRealDir($theme, true))) instanceof PwError) $this->showError(
				$result->getError());
		}
		$this->showMessage('success', 'appcenter/style/install');
	}

	/**
	 * 卸载
	 */
	public function uninstallAction() {
		$styleid = $this->getInput('styleid');
		/* @var $uninstall PwUninstallApplication */
		if ($styleid[0] !== 'L') {
			$url = PwApplicationHelper::acloudUrl(
				array('a' => 'forward', 'do' => 'uninstallApp', 'appid' => $styleid));
			$info = PwApplicationHelper::requestAcloudData($url);
			if ($info['code'] !== '0')
				$this->showError($info['msg']);
			else
				$this->showMessage('success');
		} else {
			$uninstall = Wekit::load('APPCENTER:service.srv.PwUninstallApplication');
			$r = $uninstall->uninstall($styleid);
			if ($r === true) $this->showMessage('success');
			$this->showError($r->getError());
		}
	}
	
	public function generateAction() {
		$addons = Wekit::load('APPCENTER:service.srv.PwInstallApplication')->getConfig(
			'style-type');
		$this->setOutput($addons, 'addons');
		unset($addons['portal']);
		$this->setOutput($addons, 'support');
	}
	
	public function doGenerateAction() {
		list($style_type, $name, $alias, $description, $version, $pwversion, $website) =
		$this->getInput(array('style_type', 'name', 'alias', 'description', 'version', 'pwversion', 'website'), 'post');
		if (!$style_type || !$name || !$alias || !$version || !$pwversion) $this->showError('APPCENTER:empty');
		if (!preg_match('/^[a-z][a-z0-9]+$/i', $alias)) $this->showError('APPCENTER:illegal.alias');
		list($author, $email) = $this->getInput(array('author', 'email'), 'post');
		/* @var $srv PwGenerateStyle */
		$srv = Wekit::load('APPCENTER:service.srv.PwGenerateStyle');
		$srv = new PwGenerateStyle();
		$srv->setStyle_type($style_type);
		$srv->setAlias($alias);
		$srv->setName($name);
		$srv->setDescription($description);
		$srv->setVersion($version);
		$srv->setPwversion($pwversion);
		$srv->setAuthor($author);
		$srv->setEmail($email);
		$srv->setWebsite($website);
		$r = $srv->generate();
		if ($r instanceof PwError) $this->showError($r->getError());
		$this->forwardAction('appcenter/style/doInstall', array('themes' => array($r)));
	}

	/**
	 * 重新安装流程
	 *
	 * @param string $manifestFile        	
	 * @param string $package        	
	 */
	private function _install($pack) {
		/* @var $install PwInstallApplication */
		Wind::import('APPCENTER:service.srv.PwInstallApplication');
		$install = new PwInstallApplication();
		/* @var $_install PwStyleInstall */
		$_install = Wekit::load('APPCENTER:service.srv.do.PwStyleInstall');
		$conf = $install->getConfig('install-type', 'style');
		$manifest = $pack . '/Manifest.xml';
		if (!is_file($manifest)) $this->showError('APPCENTER:install.mainfest.not.exist');
		$r = $install->initInstall($manifest);
		if ($r instanceof PwError) $this->showError($r->getError());
		$r = $_install->install($install);
		if ($r instanceof PwError) $this->showError($r->getError());
		$r = $_install->registeApplication($install);
		if ($r instanceof PwError) $this->showError($r->getError());
		$install->addInstallLog('packs', $pack);
		$install->addInstallLog('service', $conf);
		$fields = array();
		foreach ($install->getInstallLog() as $key => $value) {
			$_tmp = array(
				'app_id' => $install->getAppId(), 
				'log_type' => $key, 
				'data' => $value, 
				'created_time' => Pw::getTime(), 
				'modified_time' => Pw::getTime());
			$fields[] = $_tmp;
		}
		Wekit::load('APPCENTER:service.PwApplicationLog')->batchAdd($fields);
	}

	/**
	 * 上传
	 *
	 * @param string $key        	
	 * @return string
	 */
	private function _upload($key) {
		Wind::import('SRV:upload.action.PwIconUpload');
		Wind::import('LIB:upload.PwUpload');
		$bhv = new PwIconUpload($key, 'background/');
		$upload = new PwUpload($bhv);
		$r = $upload->execute();
		if ($r instanceof PwError) $this->showError($r->getError());
		return $bhv->getAttachInfo();
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
	 * @return PwStyle
	 */
	private function _styleDs() {
		return Wekit::load('APPCENTER:service.PwStyle');
	}

	/**
	 *
	 * @return PwStyleService
	 */
	private function _styleService() {
		return Wekit::load("APPCENTER:service.srv.PwStyleService");
	}
	
	/**
	 * @return PwCssCompile
	 */
	private function _compilerService() {
		return Wekit::load('APPCENTER:service.srv.PwCssCompile');
	}
}

?>
