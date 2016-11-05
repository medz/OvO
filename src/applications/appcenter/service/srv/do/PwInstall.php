<?php
Wind::import('APPCENTER:service.srv.iPwInstall');
Wind::import('APPCENTER:service.dm.PwApplicationDm');
Wind::import('APPCENTER:service.srv.helper.PwSystemHelper');
/**
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwInstall.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package wind
 */
class PwInstall implements iPwInstall {
	const TARGET = 'EXT:';
	const DB_TABLE = 'conf/data.sql';
	const CONTROLLER = 'controller/IndexController.php';
	const ADMIN = 'admin/ManageController.php';
	
	/*
	 * (non-PHPdoc) @see iPwInstall::unInstall()
	 */
	public function unInstall($uninstall) {
		if ($table = $uninstall->getInstallLog('table')) {
			try {
				/* @var $db WindConnection */
				$db = Wind::getComponent('db');
				foreach ($table as $key => $value) {
					$db->execute('DROP TABLE IF EXISTS `' . $key . '`');
				}
			} catch (Exception $e) {}
		}
		if ($inject = $uninstall->getInstallLog('inject')) $this->_loadPwHookInject()->batchDel(
			$inject);
		if ($hooks = $uninstall->getInstallLog('hook')) $this->_loadPwHooks()->batchDelByName(
			$hooks);
		if ($appId = $uninstall->getInstallLog('appId')) $this->_load()->delByAppId($appId);
		if ($packs = $uninstall->getInstallLog('packs')) {
			/*
			 * foreach ($packs as $value) { if (is_dir($value))
			 * WindFolder::rm($value, true); if (is_file($value))
			 * WindFile::del($value); }
			 */
		}
		return true;
	}
	
	/*
	 * (non-PHPdoc) @see iPwInstall::install()
	 */
	public function install($install) {
		$manifest = $install->getManifest();
		
		$appId = $install->getAppId();
		$result = $this->_load()->findByAppId($appId);
		
		if ($result instanceof PwError) return $result;
		if ($result) return new PwError('APPCENTER:install.exist.fail', array('{{error}}' => $appId));
		$alias = $manifest->getApplication('alias');
		if (!$alias) return new PwError('APPCENTER:install.fail.alias.empty');
		/*
		 * if (!preg_match('/^[a-z][a-z0-9]+$/i', $alias)) return new
		 * PwError('APPCENTER:illegal.alias');
		 */
		$result = $this->_load()->findByAlias($alias);
		if ($result instanceof PwError) return $result;
		if ($result) return new PwError('APPCENTER:install.exist.fail', array('{{error}}' => $alias));
		
		$hooks = $manifest->getHooks();
		if ($hooks) {
			$result = $this->_loadPwHooks()->batchFetchByName(array_keys($hooks));
			if ($result) {
				return new PwError('HOOK:hook.exit', 
					array('{{error}}' => implode(',', array_keys($result))));
			}
		}
		
		$inject = $manifest->getInjectServices();
		if ($inject) {
			$hookNames = array();
			foreach ($inject as $value) {
				if (array_key_exists($value['hook_name'], $hooks)) continue;
				$hookNames[] = $value['hook_name'];
			}
			if ($hookNames) {
				$hook = $this->_loadPwHooks()->batchFetchByName(array_unique($hookNames));
				$result = $this->_loadPwHookInject()->fetchByHookName(array_unique($hookNames));
				if ($result) {
					$injects = array();
					foreach ($result as $v) {
						$injects[$v['hook_name']][] = $v['alias'];
					}
					foreach ($inject as $key => $value) {
						$_hookName = $value['hook_name'];
						if (isset($injects[$_hookName]) && in_array($value['alias'], $injects[$_hookName])) {
							return new PwError('HOOK:inject.exit', array('{{error}}' => $value['alias']));
						}
					}
				}
			}
		}
		file_put_contents(DATA_PATH . 'tmp/log', 'checkinstall!', FILE_APPEND);
		return true;
	}
	
	/*
	 * (non-PHPdoc) @see iPwInstall::backUp()
	 */
	public function backUp($upgrade) {
		/* @var $upgrade PwUpgradeApplication */
		if ($table = $upgrade->getBackLog('table')) {
			$upgrade->setRevertLog('table', $table);
			// 更新时不动原先数据
			/*
			 * try { $db = Wind::getComponent('db'); foreach ($table as $key =>
			 * $value) { $db->execute('DROP TABLE IF EXISTS `' . $key . '`'); }
			 * } catch (Exception $e) {}
			 */
		}
		if ($inject = $upgrade->getBackLog('inject')) {
			$upgrade->setRevertLog('inject', $this->_loadPwHookInject()->fetch($inject));
			$this->_loadPwHookInject()->batchDel($inject);
		}
		if ($hooks = $upgrade->getBackLog('hook')) {
			$upgrade->setRevertLog('hook', $this->_loadPwHooks()->batchFetchByName($hooks));
			$this->_loadPwHooks()->batchDelByName($hooks);
		}
		if ($appId = $upgrade->getBackLog('appId')) {
			$upgrade->setRevertLog('appId', $this->_load()->findByAppId($appId));
			$this->_load()->delByAppId($appId);
		}
		if ($packs = $upgrade->getBackLog('packs')) {
			$targetDir = $upgrade->getTmpPath() . '/bak/';
			$log = array();
			foreach ($packs as $k => $value) {
				$target = $upgrade->getTmpPath() . '/' . basename($value) . '_' . $k . '.bak';
				PwApplicationHelper::mvSourcePack($value, $target);
				$log[] = array($value, $target);
			}
			$upgrade->setRevertLog('packs', $log);
		}
		return true;
	}
	
	/*
	 * (non-PHPdoc) @see iPwInstall::revert()
	 */
	public function revert($upgrade) {
		/* @var $upgrade PwUpgradeApplication */
		if ($table = $upgrade->getRevertLog('table')) {
			// 更新时不动原先数据
			/*
			 * try { $db = Wind::getComponent('db'); foreach ($table as $key =>
			 * $value) { $db->execute($value); } } catch (Exception $e) {}
			 */
		}
		if ($inject = $upgrade->getRevertLog('inject')) {
			$this->_loadPwHookInject()->batchAdd($inject);
		}
		if ($hooks = $upgrade->getRevertLog('hook')) {
			$this->_loadPwHooks()->batchAdd($hooks);
		}
		if ($app = $upgrade->getRevertLog('appId')) {
			$dm = new PwApplicationDm();
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
			$this->_load()->add($dm);
		}
		if ($packs = $upgrade->getRevertLog('packs')) {
			foreach ($packs as $value) {
				PwApplicationHelper::mvSourcePack($value[1], $value[0]);
			}
		}
		return true;
	}

	/**
	 *
	 * @see iPwInstall::afterInstall()
	 * @param PwInstallApplication $install        	
	 */
	public function afterInstall($install) {
		if ($install->getTmpPackage()) {
			$r = $this->registeResource($install);
			if ($r instanceof PwError) return $r;
			$name = $install->getManifest()->getApplication('alias');
			$writable = PwSystemHelper::checkWriteAble(EXT_PATH . $name . '/');
			if (!$writable) return new PwError('APPCENTER:install.mv.fail', 
				array('{{error}}' => 'EXT:' . $name));
			
			$targetPath = EXT_PATH . $name;
			PwApplicationHelper::mvSourcePack($install->getTmpPackage(), $targetPath);
			$install->addInstallLog('packs', $targetPath);
		}
		file_put_contents(DATA_PATH . 'tmp/log', 'afterinstall!', FILE_APPEND);
		return true;
	}

	/**
	 *
	 * @see iPwInstall::rollback()
	 * @param PwInstallApplication $install        	
	 */
	public function rollback($install) {
		if ($appId = $install->getInstallLog('appId')) $this->_load()->delByAppId($appId);
		if ($hooks = $install->getInstallLog('hook')) $this->_loadPwHooks()->batchDelByName($hooks);
		if ($inject = $install->getInstallLog('inject')) $this->_loadPwHookInject()->batchDel(
			$inject);
		if ($table = $install->getInstallLog('table')) {
			try {
				/* @var $db WindConnection */
				$db = Wind::getComponent('db');
				foreach ($table as $value) {
					$db->execute('DROP TABLE IF EXISTS `' . $value . '`');
				}
			} catch (Exception $e) {}
		}
		return true;
	}

	/**
	 * 注册数据文件
	 *
	 * @param PwInstallApplication $install        	
	 * @return PwError true
	 */
	public function registeData($install) {
		try {
			$sqlFile = $install->getTmpPackage() . '/' . self::DB_TABLE;
			if (!is_file($sqlFile)) return true;
			$strSql = WindFile::read($sqlFile);
			/* @var $db WindConnection */
			$db = Wind::getComponent('db');
			$sql = PwApplicationHelper::sqlParser($strSql, $db->getConfig('charset', '', 'utf8'), 
				$db->getTablePrefix(), $db->getConfig('engine', '', 'MYISAM'));
			if ($sql['CREATE']) {
				foreach ($sql['CREATE'] as $table => $statement) {
					$db->execute($statement);
				}
			}
			$install->setInstallLog('table', $sql['CREATE']);
			foreach ($sql as $option => $statements) {
				if (!in_array($option, array('INSERT', 'UPDATE', 'REPLACE', 'ALTER'))) continue;
				foreach ($statements as $table => $statement) {
					if ($option == 'ALTER') {
						if (preg_match(
							'/^ALTER\s+TABLE\s+`?(\w+)`?\s+(DROP|ADD)\s+(KEY|INDEX|UNIQUE)\s+([\w\(\),`]+)?/i', 
							$statement, $matches)) {
							list($key, $fields) = explode('(', $matches[4]);
							$fields = trim($fields, '),');
							list($matches[3]) = explode(' ', $matches[3]);
							$matches[3] = trim(strtoupper($matches[3]));
							PwSystemHelper::alterIndex(
								array(
									$matches[1], 
									$key, 
									$fields ? $fields : '', 
									$matches[3], 
									$matches[2]), $db);
						} elseif (preg_match(
							'/^ALTER\s+TABLE\s+`?(\w+)`?\s+(CHANGE|DROP|ADD)\s+`?(\w+)`?/i', 
							$statement, $matches)) {
							PwSystemHelper::alterField(array($matches[1], $matches[3], $statement), 
								$db);
						} else {
							$db->execute($statement);
						}
					} else {
						if ($option == 'INSERT') {
							$statement = 'REPLACE' . substr($statement, 6);
						}
						$db->execute($statement);
					}
				}
			}
			return true;
		} catch (Exception $e) {
			return new PwError('APPCENTER:install.fail', array('{{error}}' => $e->getMessage()));
		}
		file_put_contents(DATA_PATH . 'tmp/log', 'registedata!', FILE_APPEND);
	}

	/**
	 * 注册钩子信息
	 *
	 * @param PwInstallApplication $install        	
	 * @return PwError true
	 */
	public function registeHooks($install) {
		$manifest = $install->getManifest();
		$hooks = $manifest->getHooks();
		if (!$hooks) return true;
		foreach ($hooks as $key => $hook) {
			$hook['app_id'] = $install->getAppId();
			$hook['app_name'] = $install->getManifest()->getApplication('name');
			$hooks[$key] = $hook;
		}
		$this->_loadPwHooks()->batchAdd($hooks);
		$install->setInstallLog('hook', array_keys($hooks));
		return true;
	}

	/**
	 * 注册注入服务
	 *
	 * @param PwInstallApplication $install        	
	 * @return true PwError
	 */
	public function registeInjectServices($install) {
		$inject = $install->getManifest()->getInjectServices();
		if (!$inject) return true;
		$alias = $hookName = array();
		foreach ($inject as $key => &$value) {
			$value['app_id'] = $install->getManifest()->getApplication('alias');
			$value['app_name'] = $install->getManifest()->getApplication('name');
			$alias[] = $value['alias'];
			$hookName[] = $value['hook_name'];
		}
		$this->_loadPwHookInject()->batchAdd($inject);
		$injects = $this->_loadPwHookInject()->batchFetchByAlias($alias);
		foreach ($injects as $value) {
			if (!in_array($value['hook_name'], $hookName)) continue;
			$install->addInstallLog('inject', $value['id']);
		}
		file_put_contents(DATA_PATH . 'tmp/log', 'inject!', FILE_APPEND);
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
		$application = new PwApplicationDm();
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
		$application->setCreatedTime(Pw::getTime());
		$application->setModifiedTime(Pw::getTime());
		// 1 - 前台入口 2 - 后台入口 4 - 非纯在线 8 - 站内应用
		$status = 0;
		if ($tmp = $install->getTmpPackage()) {
			if (is_file($tmp . '/' . self::CONTROLLER)) {
				$status |= 1;
			}
			if (is_file($tmp . '/' . self::ADMIN)) {
				$status |= 2;
			}
			$status |= 4;
		}
		if ($install->getManifest()->getApplication('station', '0')) {
			$status |= 8;
		}
		$application->setStatus($status);
		if (!$application->beforeAdd()) return new PwError('APPCENTER:install.mainfest.fail');
		$this->_load()->add($application);
		$install->setInstallLog('appId', $install->getAppId());
		file_put_contents(DATA_PATH . 'tmp/log', 'app!', FILE_APPEND);
		return true;
	}

	/**
	 * 注册静态资源
	 *
	 * @param PwInstallApplication $install        	
	 * @return PwError true
	 */
	public function registeResource($install) {
		$manifest = $install->getManifest()->getManifest();
		if (!isset($manifest['res']) || !$manifest['res']) return true;
		$name = $install->getManifest()->getApplication('alias');
		$source = $install->getTmpPackage() . '/' . str_replace('.', '/', $manifest['res']);
		$targetPath = Wind::getRealDir('THEMES:extres', true);
		if (!is_dir($source)) return true;
		$writable = PwSystemHelper::checkWriteAble($targetPath . '/');
		if (!$writable) return new PwError('APPCENTER:install.mv.fail', 
			array('{{error}}' => 'THEMES:extres.' . $name));
		PwApplicationHelper::copyRecursive($source, $targetPath . '/' . $name);
		$install->addInstallLog('packs', $targetPath . '/' . $name);
		file_put_contents(DATA_PATH . 'tmp/log', 'res!', FILE_APPEND);
	}

	/**
	 *
	 * @return PwHookInject
	 */
	private function _loadPwHookInject() {
		return Wekit::load('SRV:hook.PwHookInject');
	}

	/**
	 *
	 * @return PwHooks
	 */
	private function _loadPwHooks() {
		return Wekit::load('SRV:hook.PwHooks');
	}

	/**
	 *
	 * @return PwApplication
	 */
	private function _load() {
		return Wekit::load('APPCENTER:service.PwApplication');
	}
}

?>