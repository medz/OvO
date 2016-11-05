<?php
Wind::import('APPCENTER:service.srv.do.PwInstall');
Wind::import('APPCENTER:service.dm.PwStyleDm');
Wind::import('APPCENTER:service.srv.helper.PwSystemHelper');
/**
 * 风格安装流程bp
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwStyleInstall.php 28799 2013-05-24 06:47:37Z yetianshi $
 * @package service.style.srv
 */
class PwStyleInstall extends PwInstall {
	
	/*
	 * (non-PHPdoc) @see PwInstall::unInstall()
	 */
	public function unInstall($uninstall) {
		if ($appId = $uninstall->getInstallLog('appId')) $this->_load()->deleteStyle($appId);
		if ($packs = $uninstall->getInstallLog('packs')) {
			/* foreach ($packs as $value) {
				if (is_dir($value)) WindFolder::rm($value, true);
				if (is_file($value)) WindFile::del($value);
			} */
		}
		return true;
	}
	
	/*
	 * (non-PHPdoc) @see PwInstall::install()
	 */
	public function install($install) {
		$manifest = $install->getManifest();
		
		$appId = $install->getAppId();
		$result = $this->_load()->findByAppId($appId);
		if ($result instanceof PwError) return $result;
		if ($result) return new PwError('APPCENTER:install.exist.fail');
		$alias = $manifest->getApplication('alias');
		if (!$alias) return new PwError('APPCENTER:install.fail.alias.empty');
		/* if (!preg_match('/^[a-z][a-z0-9]+$/i', $alias)) return new PwError('APPCENTER:illegal.alias'); */
		list($type) = $this->getStyleType($install);
		$result = $this->_load()->fetchStyleByAliasAndType($alias, $type);
		if ($result instanceof PwError) return $result;
		if ($result) return new PwError('APPCENTER:install.exist.fail', 
			array('{{error}}' => $manifest->getApplication('name')));
		file_put_contents(DATA_PATH . 'tmp/log', 'checkinstall!', FILE_APPEND);
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see PwInstall::backUp()
	 */
	public function backUp($upgrade) {
		
		/* @var $upgrade PwUpgradeApplication */
		if ($appId = $upgrade->getBackLog('appId')) {
			
			$upgrade->setRevertLog('appId', $this->_load()->findByAppId($appId));
			$this->_load()->deleteStyle($appId);
		}
		if ($packs = $upgrade->getBackLog('packs')) {
			$targetDir = $upgrade->getTmpPath() . '/bak/';
			$log = array();
			foreach ($packs as $value) {
				$target = $upgrade->getTmpPath() . '/' . basename($value) . '.bak';
				PwApplicationHelper::mvSourcePack($value, $target);
				$log[] = array($value, $target);
			}
			$upgrade->setRevertLog('packs', $log);
		}
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see PwInstall::revert()
	 */
	public function revert($upgrade) {
		/* @var $upgrade PwUpgradeApplication */
		if ($app = $upgrade->getRevertLog('appId')) {
			$dm = new PwStyleDm();
			$dm->setAppId($app['app_id']);
			$dm->setName($app['name']);
			$dm->setAlias($app['alias']);
			$dm->setVersion($app['version']);
			$dm->setPwVersion($app['pw_version']);
			$dm->setDescription($app['description']);
			$dm->setLogo($app['logo']);
			$dm->setAuthorName($app['author_name']);
			$dm->setAuthorEmail($app['author_email']);
			$dm->setAuthorIcon($app['author_icon']);
			$dm->setCreatedTime($app['created_time']);
			$dm->setModifiedTime($app['modified_time']);
			$dm->setType($app['style_type']);
			$this->_load()->addStyle($dm);
		}
		if ($packs = $upgrade->getRevertLog('packs')) {
			foreach ($packs as $value) {
				PwApplicationHelper::mvSourcePack($value[1], $value[0]);
			}
		}
		return true;
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see PwInstall::afterInstall()
	 * @param PwInstallApplication $install        	
	 */
	public function afterInstall($install) {
		list(, $pack) = $this->getStyleType($install);
		$alias = $install->getManifest()->getApplication('alias');
		$targetDir = THEMES_PATH . $pack;
		if (!PwSystemHelper::checkWriteAble($targetDir . '/')) {
			return new PwError('APPCENTER:install.mv.fail',
				array('{{error}}' => 'THEMES:' . str_replace('/', '.', $pack)));
		}
		$target = $targetDir . '/' . $alias;
		PwApplicationHelper::mvSourcePack($install->getTmpPackage(), $target);
		$install->addInstallLog('packs', $target);
		file_put_contents(DATA_PATH . 'tmp/log', 'afterinstall!', FILE_APPEND);
		return true;
	}

	/**
	 *
	 * @see PwInstall::rollback()
	 * @param PwInstallApplication $install        	
	 */
	public function rollback($install) {
		if (!$install instanceof PwInstallApplication) return false;
		if ($appId = $install->getInstallLog('appId')) $this->_load()->deleteStyle($appId);
		return true;
	}

	/**
	 * 注册应用信息
	 *
	 * @param PwInstallApplication $install        	
	 * @return PwError true
	 */
	public function registeApplication($install) {
		$manifest = $install->getManifest();
		$application = new PwStyleDm();
		$application->setAppId($install->getAppId());
		$application->setName($manifest->getApplication('name'));
		$application->setAlias($manifest->getApplication('alias'));
		$application->setVersion($manifest->getApplication('version'));
		$application->setPwVersion($manifest->getApplication('pw-version'));
		$application->setDescription($manifest->getApplication('description'));
		$application->setLogo($manifest->getApplication('logo'));
		$application->setWebsite($manifest->getApplication('website'));
		$application->setAuthorName($manifest->getApplication('author-name'));
		$application->setAuthorEmail($manifest->getApplication('author-email'));
		$application->setAuthorIcon($manifest->getApplication('author-icon'));
		$application->setCreatedTime(time());
		$application->setModifiedTime(time());
		list($type) = $this->getStyleType($install);
		$application->setType($type);
		if (!$application->beforeAdd()) return new PwError('APPCENTER:install.mainfest.fail');
		$this->_load()->addStyle($application);
		$install->setInstallLog('appId', $install->getAppId());
		file_put_contents(DATA_PATH . 'tmp/log', 'app!', FILE_APPEND);
		return true;
	}

	/**
	 *
	 * @param PwInstallApplication $install        	
	 */
	protected function getStyleType($install) {
		$allow_style_type = $install->getConfig('style-type');
		$style_type = $install->getManifest()->getApplication('style-type');
		if (!$style_type || !isset($allow_style_type[$style_type])) {
			$style_type = key($allow_style_type);
		}
		return array($style_type, $allow_style_type[$style_type][1]);
	}

	/**
	 *
	 * @return PwStyle
	 */
	private function _load() {
		return Wekit::load('APPCENTER:service.PwStyle');
	}
}

?>