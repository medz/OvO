<?php
Wind::import('APPCENTER:service.srv.helper.PwApplicationHelper');
/**
 * 生成demo代码
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwGenerateApplication.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package appcenter.service.srv
 */
class PwGenerateApplication {
	protected $name;
	protected $description;
	protected $alias;
	protected $version;
	protected $pwversion;
	protected $installation_service;
	protected $need_admin;
	protected $need_service;
	protected $baseDir;
	protected $defaultDir;
	protected $manifest;
	protected $author;
	protected $email;
	protected $website;

	/**
	 *
	 * @param field_type $website        	
	 */
	public function setWebsite($website) {
		$this->website = $website;
	}

	/**
	 *
	 * @param field_type $need_service        	
	 */
	public function setNeed_service($need_service) {
		$this->need_service = $need_service;
	}

	/**
	 *
	 * @param field_type $author        	
	 */
	public function setAuthor($author) {
		$this->author = $author;
	}

	/**
	 *
	 * @param field_type $email        	
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 *
	 * @param field_type $name        	
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 *
	 * @param field_type $description        	
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 *
	 * @param field_type $alias        	
	 */
	public function setAlias($alias) {
		$this->alias = $alias;
	}

	/**
	 *
	 * @param field_type $version        	
	 */
	public function setVersion($version) {
		$this->version = $version;
	}

	/**
	 *
	 * @param field_type $pwversion        	
	 */
	public function setPwversion($pwversion) {
		$this->pwversion = $pwversion;
	}

	/**
	 *
	 * @param field_type $installation_service        	
	 */
	public function setInstallation_service($installation_service) {
		$this->installation_service = $installation_service;
	}

	/**
	 *
	 * @param field_type $need_admin        	
	 */
	public function setNeed_admin($need_admin) {
		$this->need_admin = $need_admin;
	}

	public function __construct() {
		$this->defaultDir = Wind::getRealDir('APPCENTER:service.data.source');
	}

	public function generate() {
		$this->baseDir = Wind::getRealDir('EXT:' . $this->alias);
		if (is_dir($this->baseDir)) return new PwError('APPCENTER:alias.exist');
		WindFolder::mkRecur($this->baseDir);
		Wind::import('APPCENTER:service.srv.helper.PwSystemHelper');
		$writable = PwSystemHelper::checkWriteAble($this->baseDir . '/');
		if (!$writable) {
			return new PwError('APPCENTER:generate.copy.fail');
		}
		PwApplicationHelper::copyRecursive($this->defaultDir, $this->baseDir, array('service'));
		$manifest = WindFile::read($this->baseDir . '/Manifest.xml');
		$this->manifest = $this->_strtr($manifest);
		$this->_generateAdmin();
		WindFile::write($this->baseDir . '/Manifest.xml', $this->manifest);
		$this->_resolveTemplate();
		$this->_generateService();
		return true;
	}

	public function generateBaseInfo() {
		$this->baseDir = Wind::getRealDir('EXT:' . $this->alias);
		$file = $this->baseDir . '/Manifest.xml';
		Wind::import('WIND:parser.WindXmlParser');
		$parser = new WindXmlParser();
		$manifest = $parser->parse($file);
		$manifest['application']['name'] = $this->name;
		$manifest['application']['description'] = $this->description;
		$manifest['application']['version'] = $this->version;
		$manifest['application']['pw-version'] = $this->pwversion;
		$manifest['application']['author-name'] = $this->author;
		$manifest['application']['author-email'] = $this->email;
		$manifest['application']['website'] = $this->website;
		$parser = new WindXmlParser();
		$manifest = str_replace('><', ">\n\t<", $parser->parseToXml(array('manifest' => $manifest), Wind::getApp()->getResponse()->getCharset()));
		if (!WindFile::write($file, $manifest)) {
			return new PwError('APPCENTER:generate.copy.fail');
		}
	}

	/**
	 * 生成钩子
	 *
	 * @param unknown_type $hookname        	
	 * @return PwError
	 */
	public function generateHook($hookname) {
		$prefix = substr($hookname, 0, 2);
		if ('s_' == $prefix) {
			return $this->generateSimpleHook($hookname);
		} elseif ('m_' == $prefix) {
			return $this->generateServiceHook($hookname);
		} else {
			return new PwError('APPCENTER:generate.unsupport.hook');
		}
	}

	public function generateServiceHook($hookname) {
		$hookInfo = Wekit::load('hook.PwHooks')->fetchByName($hookname);
		list($description, , $interface) = explode("\r\n", $hookInfo['document']);
		if (!$interface) return new PwError('APPCENTER:generate.unsupport.hook');
		$this->baseDir = Wind::getRealDir('EXT:' . $this->alias);
		$thing = substr($hookname, 2);
		$classname = $this->_ucwords($this->alias . $thing) . 'Do';
		$interfacename = Wind::import($interface);
		$reflection = new ReflectionClass($interfacename);
		$extends = ($reflection->isInterface() ? 'implements ' : 'extends ') . $interfacename;
		$manifest = Wind::getComponent('configParser')->parse($this->baseDir . '/Manifest.xml');
		$hook = array(
			'app_' . $this->alias => array(
				'class' => 'EXT:' . $this->alias . '.service.srv.' . $classname, 
				'description' => 'this is another ' . $hookname));
		$manifest['inject-services'][$hookname] = $hook;
		Wind::import('WIND:parser.WindXmlParser');
		$parser = new WindXmlParser();
		$manifest = str_replace('><', ">\n\t<", $parser->parseToXml(array('manifest' => $manifest), Wind::getApp()->getResponse()->getCharset()));
		if (!WindFile::write($this->baseDir . '/Manifest.xml', $manifest)) {
			return new PwError('APPCENTER:generate.copy.fail');
		}
		
		$class = WindFile::read(dirname($this->defaultDir) . '/servicehook');
		$class = strtr($class, 
			array(
				'{{interface}}' => $interface, 
				'{{classname}}' => $classname, 
				'{{extends}}' => $extends, 
				'{{interfacename}}' => $interfacename, 
				'{{classname}}' => $classname, 
				'{{author}}' => $this->author, 
				'{{email}}' => $this->email, 
				'{{website}}' => $this->website, 
				'{{description}}' => $description));
		WindFolder::mkRecur($this->baseDir . '/service/srv/');
		if (!WindFile::write($this->baseDir . '/service/srv/' . $classname . '.php', $class)) {
			return new PwError('APPCENTER:generate.copy.fail');
		}
	}

	/**
	 * 生成简单钩子
	 *
	 * @param unknown_type $hookname        	
	 */
	public function generateSimpleHook($hookname) {
		$hookInfo = Wekit::load('hook.PwHooks')->fetchByName($hookname);
		list($description, $doc) = explode("\r\n", $hookInfo['document']);
		if (!$doc) return new PwError('APPCENTER:generate.unsupport.hook');
		preg_match_all('/\$[a-zA-Z_][a-zA-Z0-9_]*/', $doc, $matches);
		$param = implode(', ', $matches[0]);
		$doc = str_replace("\n", "\n\t * ", $doc);
		$this->baseDir = Wind::getRealDir('EXT:' . $this->alias);
		$manifest = Wind::getComponent('configParser')->parse($this->baseDir . '/Manifest.xml');
		$thing = substr($hookname, 2);
		$classname = $this->_ucwords($this->alias . '_' . $thing) . 'Do';
		$method = WindUtility::lcfirst($this->_ucwords($this->alias)) . 'Do';
		$hook = array(
			'app_' . $this->alias => array(
				'class' => 'EXT:' . $this->alias . '.service.srv.' . $classname, 
				'loadway' => 'load', 
				'method' => $method, 
				'description' => 'this is another ' . $hookname));
		$manifest['inject-services'][$hookname] = $hook;
		Wind::import('WIND:parser.WindXmlParser');
		$parser = new WindXmlParser();
		$manifest = str_replace('><', ">\n\t<", $parser->parseToXml(array('manifest' => $manifest), Wind::getApp()->getResponse()->getCharset()));
		if (!WindFile::write($this->baseDir . '/Manifest.xml', $manifest)) {
			return new PwError('APPCENTER:generate.copy.fail');
		}
		
		$class = WindFile::read(dirname($this->defaultDir) . '/simplehook');
		$class = strtr($class, 
			array(
				'{{method}}' => $method, 
				'{{classname}}' => $classname, 
				'{{document}}' => $doc, 
				'{{param}}' => $param, 
				'{{classname}}' => $classname, 
				'{{author}}' => $this->author, 
				'{{email}}' => $this->email, 
				'{{website}}' => $this->website, 
				'{{description}}' => $description));
		WindFolder::mkRecur($this->baseDir . '/service/srv/');
		if (!WindFile::write($this->baseDir . '/service/srv/' . $classname . '.php', $class)) {
			return new PwError('APPCENTER:generate.copy.fail');
		}
	}

	private function _resolveTemplate() {
		$index = $this->baseDir . '/template/index_run.htm';
		$admin = $this->baseDir . '/template/admin/manage_run.htm';
		WindFile::write($index, strtr(WindFile::read($index), array('{{alias}}' => $this->alias)));
		WindFile::write($admin, strtr(WindFile::read($admin), array('{{alias}}' => $this->alias)));
	}

	private function _strtr($string) {
		return strtr($string, 
			array(
				'{{charset}}' => Wind::getApp()->getResponse()->getCharset(), 
				'{{name}}' => $this->name, 
				'{{alias}}' => $this->alias, 
				'{{version}}' => $this->version, 
				'{{pw_version}}' => $this->pwversion, 
				'{{author}}' => $this->author, 
				'{{email}}' => $this->email, 
				'{{website}}' => $this->website, 
				'{{description}}' => $this->description, 
				'{{installation_service}}' => $this->installation_service));
	}

	private function _generateAdmin() {
		if ($this->need_admin) {
			$classname = $this->_ucwords($this->alias) . '_ConfigDo';
			$hook = array(
				's_admin_menu' => array(
					'app_' . $this->alias => array(
						'class' => 'EXT:' . $this->alias . '.service.srv.' . $classname, 
						'loadway' => 'load', 
						'method' => 'getAdminMenu', 
						'description' => $this->name . 'admin menu')));
			Wind::import('WIND:parser.WindXmlParser');
			$parser = new WindXmlParser();
			$hook = preg_replace('/<\?xml[^\?]+\?>/i', '', $parser->parseToXml($hook, Wind::getApp()->getResponse()->getCharset()));
			$hook = str_replace('><', ">\n\t<", $hook);
			$this->manifest = strtr($this->manifest, array('{{inject_services}}' => $hook));
			$class = WindFile::read(dirname($this->defaultDir) . '/hook.admin');
			$class = strtr($class, 
				array(
					'{{alias}}' => $this->alias, 
					'{{name}}' => $this->name, 
					'{{classname}}' => $classname, 
					'{{author}}' => $this->author, 
					'{{email}}' => $this->email, 
					'{{website}}' => $this->website));
			WindFolder::mkRecur($this->baseDir . '/service/srv/');
			WindFile::write($this->baseDir . '/service/srv/' . $classname . '.php', $class);
		} else {
			$this->manifest = strtr($this->manifest, array('{{inject_services}}' => ''));
			WindFolder::clearRecur($this->baseDir . '/admin', true);
			WindFolder::clearRecur($this->baseDir . '/template/admin', true);
		}
	}

	protected function _generateService() {
		if (!$this->need_service) return true;
		$prefix = 'app_' . $this->alias . '_table';
		$classFrefix = str_replace(' ', '', ucwords('app_ ' . $this->alias . '_ ' . $this->alias));
		WindFolder::mkRecur($this->baseDir . '/service/dao/');
		WindFolder::mkRecur($this->baseDir . '/service/dm/');
		
		$dao_file = $this->defaultDir . '/service/dao/defaultdao';
		$class_dao = $classFrefix . 'Dao';
		$dao = strtr(WindFile::read($dao_file), 
			array(
				'{{classname}}' => $class_dao, 
				'{{prefix}}' => $prefix, 
				'{{author}}' => $this->author, 
				'{{email}}' => $this->email, 
				'{{website}}' => $this->website));
		
		WindFile::write($this->baseDir . '/service/dao/' . $class_dao . '.php', $dao);
		
		$dm_file = $this->defaultDir . '/service/dm/defaultdm';
		$class_dm = $classFrefix . 'Dm';
		$dm = strtr(WindFile::read($dm_file), 
			array(
				'{{classname}}' => $class_dm, 
				'{{author}}' => $this->author, 
				'{{email}}' => $this->email, 
				'{{website}}' => $this->website));
		WindFile::write($this->baseDir . '/service/dm/' . $class_dm . '.php', $dm);
		
		$ds_file = $this->defaultDir . '/service/defaultds';
		$class_ds = $classFrefix;
		$ds = strtr(WindFile::read($ds_file), 
			array(
				'{{classname}}' => $class_ds, 
				'{{alias}}' => $this->alias, 
				'{{class_dm}}' => $class_dm, 
				'{{class_dao}}' => $class_dao, 
				'{{author}}' => $this->author, 
				'{{email}}' => $this->email, 
				'{{website}}' => $this->website));
		WindFile::write($this->baseDir . '/service/' . $class_ds . '.php', $ds);
	}

	protected function _ucwords($str) {
		return 'App_' . str_replace('_ ', '_', ucwords(str_replace('_', '_ ', $str)));
	}
}

?>