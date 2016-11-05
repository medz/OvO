<?php
Wind::import('ADMIN:library.AdminBaseController');
Wind::import('APPCENTER:service.srv.PwGenerateApplication');
/**
 * 开发助手
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: DevelopController.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package appcenter.admin
 */
class DevelopController extends AdminBaseController {
	public function run() {
		$this->_installService();
	}
	
	public function doRunAction() {
		$this->_generate();
	}
	
	/**
	 * 编辑
	 *
	 */
	public function editAction() {
		$alias = $this->getInput('alias', 'get');
		/* @var $app PwApplication */
		$app = Wekit::load('APPCENTER:service.PwApplication');
		$app = $app->findByAlias($alias);
		$this->setOutput($app, 'app');
	}
	
	/**
	 * 编辑xml
	 *
	 */
	public function editxmlAction() {
		$alias = $this->getInput('alias', 'get');
		/* @var $app PwApplication */
		$app = Wekit::load('APPCENTER:service.PwApplication');
		$app = $app->findByAlias($alias);
		$this->setOutput($app, 'app');
		$manifest = WindFile::read(Wind::getRealPath('EXT:' . $alias . '.Manifest.xml', true));
		$this->setOutput($manifest, 'manifest');
	}
	
	/**
	 * 编辑我的扩展
	 *
	 */
	public function edithookAction() {
		$alias = $this->getInput('alias', 'get');
		/* @var $app PwApplication */
		$appDs = Wekit::load('APPCENTER:service.PwApplication');
		$app = $appDs->findByAlias($alias);
		$this->setOutput($app, 'app');
		
		$myHooks = Wekit::load('hook.PwHookInject')->findByAppId($alias);
		$this->setOutput(array('myHooks' => $myHooks));
	}
	
	/**
	 * 显示添加扩展页面
	 *
	 */
	public function addhookAction() {
		Wind::import('SRV:hook.dm.PwHookSo');
		$hooks = Wekit::load('hook.PwHooks')->fetchList(0);
		$hooks = array_reverse($hooks, true);
		$this->setOutput($this->getInput('alias'), 'alias');
		$this->setOutput(array('hooks' => $hooks));
	}
	
	/**
	 * 添加扩展 提交
	 *
	 */
	public function doEditHookAction() {
		list($hookname, $alias) = $this->getInput(array('hook_name', 'alias'));
		/* @var $app PwApplication */
		$appDs = Wekit::load('APPCENTER:service.PwApplication');
		$appInfo = $appDs->findByAlias($alias);
		$app = new PwGenerateApplication();
		$app->setAlias($alias);
		$app->setName($appInfo['name']);
		$app->setDescription($appInfo['description']);
		$app->setVersion($appInfo['version']);
		$app->setPwversion($appInfo['pwversion']);
		$app->setAuthor($appInfo['author_name']);
		$app->setEmail($appInfo['author_email']);
		$app->setWebsite($appInfo['website']);
		$r = $app->generateHook($hookname);
		if ($r instanceof PwError) {
			$this->showError($r->getError());
		}
		Wekit::load('APPCENTER:service.srv.PwDebugApplication')->compile();
		$this->showMessage('success');
	}
	
	public function doEditAction() {
		list($appid, $name, $alias, $description, $version, $pwversion, $author, $email, $website) =
		$this->getInput(array('appid', 'name', 'alias', 'description', 'version', 'pwversion', 'author', 'email', 'website'), 'post');
		if (!$name || !$alias || !$version || !$pwversion) $this->showError('APPCENTER:empty');
		if (!preg_match('/^[a-z][a-z0-9]+$/i', $alias)) $this->showError('APPCENTER:illegal.alias');
		$app = new PwGenerateApplication();
		$app->setAlias($alias);
		$app->setName($name);
		$app->setDescription($description);
		$app->setVersion($version);
		$app->setPwversion($pwversion);
		$app->setAuthor($author);
		$app->setEmail($email);
		$app->setWebsite($website);
		$r = $app->generateBaseInfo();
		if ($r instanceof PwError) $this->showError($r->getError());
		Wekit::load('APPCENTER:service.srv.PwDebugApplication')->compile();
		$this->showMessage('success', 'appcenter/develop/edit?alias=' . $alias);
	}
	
	public function doEditXmlAction() {
		list($xml, $alias) = $this->getInput(array('xml', 'alias'), 'post');
		$file = Wind::getRealDir('EXT:' . $alias) . '/Manifest.xml';
		Wind::import('WIND:parser.WindXmlParser');
		$parser = new WindXmlParser();
		if (!$parser->parseXmlStream($xml)) $this->showError('APPCENTER:xml.fail');
		$r = WindFile::write($file, $xml);
		if (!$r) {
			$this->showError('APPCENTER:generate.copy.fail');
		}
		Wekit::load('APPCENTER:service.srv.PwDebugApplication')->compile(true);
		$this->showMessage('success');
	}
	
	private function _generate() {
		list($name, $alias, $description, $version, $pwversion, $service, $need_admin, $need_service, $website) =
		$this->getInput(array('name', 'alias', 'description', 'version', 'pwversion', 'service', 'need_admin', 'need_service', 'website'), 'post');
		if (!$name || !$alias || !$version || !$pwversion) $this->showError('APPCENTER:empty');
		if (!preg_match('/^[a-z][a-z0-9]+$/i', $alias)) $this->showError('APPCENTER:illegal.alias');
		list($author, $email) = $this->getInput(array('author', 'email'), 'post');
		$app = new PwGenerateApplication();
		$app->setAlias($alias);
		$app->setName($name);
		$app->setDescription($description);
		$app->setVersion($version);
		$app->setPwversion($pwversion);
		$app->setAuthor($author);
		$app->setEmail($email);
		$app->setWebsite($website);
		$app->setInstallation_service($service ? implode(',', $service) : '');
		$app->setNeed_admin($need_admin);
		$app->setNeed_service($need_service);
		$r = $app->generate();
		if ($r instanceof PwError) $this->showError($r->getError());
		Wekit::load('APPCENTER:service.srv.PwDebugApplication')->installPack(EXT_PATH . $alias);
		$this->showMessage(array('APPCENTER:develop.success', array($name, $alias)), 'appcenter/app/run');
	}
	
	private function _installService($exists = array()) {
		$install = Wekit::load('APPCENTER:service.srv.PwInstallApplication');
		$temp = $install->getConfig('installation-service');
		$service = array();
		$lang = Wind::getComponent('i18n');
		foreach ($temp as $k => $v) {
			$service[$k] = $lang->getMessage($v['message']);
		}
		$this->setOutput($service, 'service');
		$keys = array_keys($service);
		$temp = array();
		foreach ($exists as $s) {
			if (isset($s['_key']) && in_array($s['_key'], $keys)) $temp[] = $s['_key'];
		}
		$this->setOutput($temp, 'exists');
	}
}

?>