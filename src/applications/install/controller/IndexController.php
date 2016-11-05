<?php
define('WIND_SETUP', 'install');
/**
 * 安装流程
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright (c)2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $$Id$$
 * @package application
 */
class IndexController extends WindController {
	/**
	 *
	 * 定义需要导入默认数据sql文件
	 *
	 * @var array
	 */
	private $wind_data = array(
		'wind_structure.sql',
		'pw_windid_area.sql',
		'pw_windid_school.sql',
		'pw_windid_config.sql',
		'pw_user_groups.sql',
		'pw_common_config.sql',
		'pw_design.sql',
		'pw_acloud.sql',
		'wind_data.sql',
		'demo_data.sql');

	/* (non-PHPdoc)
	 * @see WindSimpleController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		
		if ('finish' != $handlerAdapter->getAction()) Wekit::createapp('install');
		
		//ajax递交编码转换
		if ($this->getRequest()->getIsAjaxRequest()) {
			$toCharset = $this->getResponse()->getCharset();
			if (strtoupper(substr($toCharset, 0, 2)) != 'UT') {
				$_tmp = array();
				foreach ($_POST as $key => $value) {
					$key = WindConvert::convert($key, $toCharset, 'UTF-8');
					$_tmp[$key] = WindConvert::convert($value, $toCharset, 'UTF-8');
				}
				$_POST = $_tmp;
			}
		}
		$_consts = include (Wind::getRealPath('CONF:publish.php', true));
		foreach ($_consts as $const => $value) {
			if (defined($const)) continue;
			if ($const === 'PUBLIC_URL' && !$value) {
				$value = Wind::getApp()->getRequest()->getBaseUrl(true);
			}
			define($const, $value);
		}
		
		$url = array();
		$url['base'] = PUBLIC_URL;
		$url['res'] = WindUrlHelper::checkUrl(PUBLIC_RES, PUBLIC_URL);
		$url['css'] = WindUrlHelper::checkUrl(PUBLIC_RES . '/css/', PUBLIC_URL);
		$url['images'] = WindUrlHelper::checkUrl(PUBLIC_RES . '/images/', PUBLIC_URL);
		$url['js'] = WindUrlHelper::checkUrl(PUBLIC_RES . '/js/dev/', PUBLIC_URL);
		$url['attach'] = WindUrlHelper::checkUrl(PUBLIC_ATTACH, PUBLIC_URL);
		Wekit::setGlobal($url, 'url');
		$this->setOutput(NEXT_VERSION, 'wind_version');
		
		WindFile::isFile($this->_getInstallLockFile()) && $this->showError('INSTALL:have_install_lock');
	}

	/**
	 * 协议
	 * @see WindAction::run()
	 */
	public function run() {
		$wind_license = WindFile::read(Wind::getRealPath('ROOT:LICENSE', false));
		$this->setOutput($wind_license, 'wind_license');
	}

	/**
	 * 检测环境
	 */
	public function checkAction() {
		$lowestEnvironment = $this->_getLowestEnvironment();
		$currentEnvironment = $this->_getCurrentEnvironment();
		$recommendEnvironment = $this->_getRecommendEnvironment();
		$writeAble = $this->_checkFileRight();
		
		$check_pass = true;
		foreach ($currentEnvironment as $key => $value) {
			if (false !== strpos($key, '_ischeck') && false === $value) $check_pass = false;
		}
		foreach ($writeAble as $value) {
			if (false === $value) $check_pass = false;
		}
		
		$this->setOutput($writeAble, 'writeAble');
		$this->setOutput($lowestEnvironment, 'lowestEnvironment');
		$this->setOutput($currentEnvironment, 'currentEnvironment');
		$this->setOutput($recommendEnvironment, 'recommendEnvironment');
	}

	/**
	 * 数据库信息配置
	 */
	public function infoAction() {
		WindFile::del($this->_getTableLogFile());
		WindFile::del($this->_getTableSqlFile());
		
		$database_writable = $this->_checkWriteAble($this->_getDatabaseFile());
		$founder_writable = $this->_checkWriteAble($this->_getFounderFile());
		$this->setOutput($database_writable, 'database_writable');
		$this->setOutput($founder_writable, 'founder_writable');
	}

	/**
	 * 创建数据库
	 */
	public function databaseAction() {
		$keys = array(
			'dbhost',
			'dbuser',
			'dbname',
			'dbprefix',
			'manager',
			'manager_pwd',
			'manager_ckpwd',
			'manager_email',
			'dbpw',
			'engine');
		$input = $this->getInput($keys, 'post');
		$force = $this->getInput('force');
		$input = array_combine($keys, $input);
		foreach ($input as $k => $v) {
			if (!in_array($k, array('dbpw', 'engine')) && empty($v)) $this->showError("INSTALL:input_empty_$k");
		}
		if ($input['manager_pwd'] !== $input['manager_ckpwd']) {
			$this->showError('INSTALL:manager_pwd.eque.ckpwd');
		}
		if (!preg_match('/^[\x7f-\xff\dA-Za-z\.\_]+$/', $input['manager'])) {
			$this->showError('INSTALL:founder.name.error');
		}
		$usernameLen = Pw::strlen($input['manager']);
		$passwordLen = Pw::strlen($input['manager_pwd']);
		if ($usernameLen < 1 || $usernameLen > 15 || $passwordLen < 1 || $passwordLen > 25) {
			$this->showError('INSTALL:founder.init.fail');
		}
		if (false === WindValidator::isEmail($input['manager_email'])) {
			$this->showError('INSTALL:founder.init.email.error');
		}
				
		list($input['dbhost'], $input['dbport']) = explode(':', $input['dbhost']);
		$input['dbport'] = !empty($input['dbport']) ? intval($input['dbport']) : 3306;
		if (!empty($input['engine'])) {
			$input['engine'] = strtoupper($input['engine']);
			!in_array($input['engine'], array('MyISAM', 'InnoDB')) && $input['engine'] = 'MyISAM';
		} else {
			$input['engine'] = 'MyISAM';
		}
		$charset = Wind::getApp()->getResponse()->getCharset();
		$charset = str_replace('-', '', strtolower($charset));
		if (!in_array($charset, array('gbk', 'utf8', 'big5'))) $charset = 'utf8';
		
		// 检测是否安装过了
		Wind::import("WIND:db.WindConnection");
		$dsn = 'mysql:host=' . $input['dbhost'] . ';port=' . $input['dbport'];
		try {
			$pdo = new WindConnection($dsn, $input['dbuser'], $input['dbpw'], $charset);
			$result = $pdo->query("SHOW DATABASES")->fetchAll();
			foreach ($result as $v) {
				if ($v['Database'] == $input['dbname']) {
					$dbnameExist = true;
					break;
				}
			}
			if ($dbnameExist) {
				$result = $pdo->query("SHOW TABLES FROM `{$input['dbname']}`")->rowCount();
				empty($result) && $dbnameExist = false;
			}
		} catch (PDOException $e) {
			$error = $e->getMessage();
			$this->showError($error, false);
		}
		if ($dbnameExist && !$force) $this->showError('INSTALL:have_install', true, 'index/database', true);
		if (!$dbnameExist) {
			try {
				$pdo = new WindConnection($dsn, $input['dbuser'], $input['dbpw'], $charset);
				$pdo->query("CREATE DATABASE IF NOT EXISTS `{$input['dbname']}` DEFAULT CHARACTER SET $charset");
			} catch (PDOException $e) {
				$error = $e->getMessage();
				$this->showError($error, false);
			}
		}
		$pdo->close();
		if (!$this->_checkWriteAble($this->_getDatabaseFile())) {
			$this->showError('INSTALL:error_777_database');
		}
		if (!$this->_checkWriteAble($this->_getFounderFile())) {
			$this->showError('INSTALL:error_777_founder');
		}
		
		$database = array(
			'dsn' => 'mysql:host=' . $input['dbhost'] . ';dbname=' . $input['dbname'] . ';port=' . $input['dbport'],
			'user' => $input['dbuser'],
			'pwd' => $input['dbpw'],
			'charset' => $charset,
			'tableprefix' => $input['dbprefix'],
			'engine' => $input['engine'],
			'founder' => array(
				'manager' => $input['manager'],
				'manager_pwd' => $input['manager_pwd'],
				'manager_email' => $input['manager_email']));
		WindFile::savePhpData($this->_getTempFile(), $database);
		
		$arrSQL = array();
		foreach ($this->wind_data as $file) {
			$file = Wind::getRealPath("APPS:install.lang.$file", true);
			if (!WindFile::isFile($file)) continue;
			$content = WindFile::read($file);
			if (!empty($content)) $arrSQL = array_merge_recursive($arrSQL,
				$this->_sqlParser($content, $charset, $input['dbprefix'], $input['engine']));
		}
		WindFile::savePhpData($this->_getTableSqlFile(), $arrSQL['SQL']);
		WindFile::write($this->_getTableLogFile(), implode('<wind>', $arrSQL['LOG']['CREATE']));
		
		$this->showMessage('success', false, 'index/table');
	}

	/**
	 * 创建数据表
	 */
	public function tableAction() {
		@set_time_limit(300);
        
        $db = $this->_checkDatabase();
		
        try {
			$pdo = new WindConnection($db['dsn'], $db['user'], $db['pwd'], $db['charset']);
			$pdo->setConfig($db);
		} catch (PDOException $e) {
			$this->showError($e->getMessage(), false);
		}
		
        $tableSql = include $this->_getTableSqlFile();

		try {
			foreach ($tableSql['DROP'] as $sql) {
				$pdo->query($sql);
			}
            foreach ($tableSql['CREATE'] as $sql) {
				$pdo->query($sql);
            }
		} catch (PDOException $e) {
			$this->showError($e->getMessage(), false);
		}
		$pdo->close();
		$log = WindFile::read($this->_getTableLogFile());
		$this->setOutput($log, 'log');
	}

	/**
	 * 导入默认数据
	 */
	public function dataAction() {
		@set_time_limit(300);
		
		$db = $this->_checkDatabase();
		
		try {
			$pdo = new WindConnection($db['dsn'], $db['user'], $db['pwd'], $db['charset']);
			$pdo->setConfig($db);
		} catch (PDOException $e) {
			$this->showError($e->getMessage(), false);
		}
		
		$tableSql = include $this->_getTableSqlFile();
		try {
			foreach ($tableSql['UPDATE'] as $sql) {
				$pdo->query($sql);
			}
		} catch (PDOException $e) {
			$this->showError($e->getMessage(), false);
		}
		$pdo->close();

		//数据库配置
		$database = array(
			'dsn' => $db['dsn'],
			'user' => $db['user'],
			'pwd' => $db['pwd'],
			'charset' => $db['charset'],
			'tableprefix' => $db['tableprefix'],
			'engine' => $db['engine']);
		WindFile::savePhpData($this->_getDatabaseFile(), $database);
		
		//写入windid配置信息
		$this->_writeWindid();
		
		$this->forwardRedirect(WindUrlHelper::createUrl('index/finish'));
	}

	/**
	 * 安装完成
	 */
	public function finishAction() {
		//Wekit::createapp('phpwind');
		Wekit::C()->reload('windid');
		WindidApi::api('user');
		$db = $this->_checkDatabase();
		//更新HOOK配置数据

		Wekit::load('hook.srv.PwHookRefresh')->refresh();

		//初始化站点config
		$site_hash = WindUtility::generateRandStr(8);
		$cookie_pre = WindUtility::generateRandStr(3);
		Wekit::load('config.PwConfig')->setConfig('site', 'hash', $site_hash);
		Wekit::load('config.PwConfig')->setConfig('site', 'cookie.pre', $cookie_pre);
		Wekit::load('config.PwConfig')->setConfig('site', 'info.mail', $db['founder']['manager_email']);
		Wekit::load('config.PwConfig')->setConfig('site', 'info.url', PUBLIC_URL);
		Wekit::load('nav.srv.PwNavService')->updateConfig();
		
		Wind::import('WINDID:service.config.srv.WindidConfigSet');
		$windidConfig = new WindidConfigSet('site');
		$windidConfig->set('hash', $site_hash)
		->set('cookie.pre', $cookie_pre)
		->flush();
		
		//风格默认数据
		Wekit::load('APPCENTER:service.srv.PwStyleInit')->init();

		//计划任务默认数据
		Wekit::load('cron.srv.PwCronService')->updateSysCron();

		//更新数据缓存
		/* @var $usergroup PwUserGroupsService */
		$usergroup = Wekit::load('SRV:usergroup.srv.PwUserGroupsService');
		$usergroup->updateLevelCache();
		$usergroup->updateGroupCache(range(1, 16));
		$usergroup->updateGroupRightCache();
		/* @var $emotion PwEmotionService */
		$emotion = Wekit::load('SRV:emotion.srv.PwEmotionService');
		$emotion->updateCache();
			
		//创始人配置
		$uid = $this->_writeFounder($db['founder']['manager'], $db['founder']['manager_pwd'], $db['founder']['manager_email']);
		
		//门户演示数据
		Wekit::load('SRV:design.srv.PwDesignDefaultService')->likeModule();
		Wekit::load('SRV:design.srv.PwDesignDefaultService')->tagModule();
		Wekit::load('SRV:design.srv.PwDesignDefaultService')->reviseDefaultData();

		//演示数据导入
		Wind::import('SRV:forum.srv.PwPost');
		Wind::import('SRV:forum.srv.post.PwTopicPost');
		$pwPost = new PwPost(new PwTopicPost(2, new PwUserBo($uid)));
		$threads = $this->_getDemoThreads();
		foreach ($threads as $thread) {
			$postDm = $pwPost->getDm();
			$postDm->setTitle($thread['title'])->setContent($thread['content']);
			$result = $pwPost->execute($postDm);
		}
		
		//全局缓存更新
		Wekit::load('SRV:cache.srv.PwCacheUpdateService')->updateConfig();
		Wekit::load('SRV:cache.srv.PwCacheUpdateService')->updateMedal();
		
		//清理安装过程的文件
		WindFile::write($this->_getInstallLockFile(), 'LOCKED');
		WindFile::del($this->_getTempFile());
		WindFile::del($this->_getTableLogFile());
		WindFile::del($this->_getTableSqlFile());
		
	}

	/* (non-PHPdoc)
	 * @see WindSimpleController::setDefaultTemplateName()
	 */
	protected function setDefaultTemplateName($handlerAdapter) {
		$template = $handlerAdapter->getController() . '_' . $handlerAdapter->getAction();
		$this->setTemplate(strtolower($template));
	}

	/**
	 * 显示信息
	 *
	 * @param string $message 消息信息
	 * @param string $referer 跳转地址
	 * @param boolean $referer 是否刷新页面
	 * @param string $action 处理句柄
	 * @see WindSimpleController::showMessage()
	 */
	protected function showMessage($message = '', $lang = true, $referer = '', $refresh = false) {
		$this->addMessage('success', 'state');
		$this->addMessage($this->forward->getVars('data'), 'data');
		$this->showError($message, $lang, $referer, $refresh);
	}

	/**
	 * 显示错误
	 *
	 * @param array $error array('',array())
	 */
	protected function showError($error = '', $lang = true, $referer = '', $refresh = false) {
		$referer && $referer = WindUrlHelper::createUrl($referer);
		$this->addMessage($referer, 'referer');
		$this->addMessage($refresh, 'refresh');
		if ($lang) {
			$lang = Wind::getComponent('i18n');
			$error = $lang->getMessage($error);
		}
		parent::showMessage($error);
	}

	/**
	 * WIND SQL 格式解析
	 *
	 * @param string $strSQL SQL语句字串
	 * @param string $charset 字符集
	 * @return array(SQL, log)
	 */
	private function _sqlParser($strSQL, $charset, $dbprefix, $engine) {
		if (empty($strSQL)) return array();
		$query = '';
		$logData = $tableSQL = $dataSQL = $fieldSQL = array();
		$strSQL = str_replace(array("\r", "\n\n", ";\n"), array('', "\n", ";<wind>\n"), trim($strSQL, " \n\t") . "\n");
		$arrSQL = explode("\n", $strSQL);
		foreach ($arrSQL as $value) {
			$value = trim($value, " \t");
			if (!$value || substr($value, 0, 2) === '--') continue;
			$query .= $value;
			if (substr($query, -7) != ';<wind>') continue;
			$query = preg_replace('/([ `]+)pw_/', "\${1}$dbprefix", $query, 1);
			$sql_key = strtoupper(substr($query, 0, strpos($query, ' ')));
			if ($sql_key == 'CREATE') {
				$tablename = trim(strrchr(trim(substr($query, 0, strpos($query, '('))), ' '), '` ');
				$query = str_replace(array('ENGINE=MyISAM', 'DEFAULT CHARSET=utf8', ';<wind>'),
					array("ENGINE=$engine", "DEFAULT CHARSET=$charset", ';'), $query);
				$dataSQL['CREATE'][] = $query;
				$logData['CREATE'][] = $tablename;
			} elseif ($sql_key == 'DROP') {
				$tablename = trim(strrchr(trim(substr($query, 0, strrpos($query, ';'))), ' '), '` ');
				$query = str_replace(';<wind>', '', $query);
				$dataSQL['DROP'][] = $query;
				//$logData['DROP'][] = $tablename;
			} elseif ($sql_key == 'ALTER') {
				$query = str_replace(';<wind>', '', $query);
				$dataSQL['ALTER'][] = $query;
				//$logData['ALTER'][] = $query;
			} elseif (in_array($sql_key, array('INSERT', 'REPLACE', 'UPDATE'))) {
				$query = str_replace(';<wind>', '', $query);
				$sql_key == 'INSERT' && $query = 'REPLACE' . substr($query, 6);
				$dataSQL['UPDATE'][] = $query;
				//$logData['UPDATE'][] = $query;
			}
			$query = '';
		}
		return array('SQL' => $dataSQL, 'LOG' => $logData);
	}

	/**
	 * 获得当前的环境信息
	 *
	 * @return array
	 */
	private function _getCurrentEnvironment() {
		$lowestEnvironment = $this->_getLowestEnvironment();
		$rootPath = Wind::getRealDir('ROOT:');
		$space = floor(@disk_free_space($rootPath) / (1024 * 1024));
		$space = !empty($space) ? $space . 'M': 'unknow';
		$currentUpload = ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow';
		$upload_ischeck = intval($currentUpload) >= intval($lowestEnvironment['upload']) ? true : false;
		$space_ischeck = intval($space) >= intval($lowestEnvironment['space']) ? true : false;
		$version_ischeck = version_compare(phpversion(), $lowestEnvironment['version']) < 0 ? false : true;
		$pdo_mysql_ischeck = extension_loaded('pdo_mysql');
		if (function_exists('mysql_get_client_info')) {
			$mysql = mysql_get_client_info();
			$mysql_ischeck = true;//version_compare($mysql, $lowestEnvironment['mysql']) < 0 ? false : true;
		} elseif (function_exists('mysqli_get_client_info')) {
			$mysql = mysqli_get_client_info();
			$mysql_ischeck = true;//version_compare($mysql, $lowestEnvironment['mysql']) < 0 ? false : true;
		} elseif ($pdo_mysql_ischeck) {
			$mysql_ischeck = true;
			$mysql = 'unknow';
		} else {
			$mysql_ischeck = false;
			$mysql = 'unknow';
		}
		if (function_exists('gd_info')) {
			$gdinfo = gd_info();
			$gd = $gdinfo['GD Version'];
			$gd_ischeck = version_compare($lowestEnvironment['gd'], $gd) < 0 ? false : true;
		} else {
			$gd_ischeck = false;
			$gd = 'unknow';
		}
		return array(
			'os_ischeck' => true,
			'version_ischeck' => $version_ischeck,
			'mysql_ischeck' => $mysql_ischeck,
			'pdo_mysql_ischeck' => $pdo_mysql_ischeck,
			'upload_ischeck' => $upload_ischeck,
			'space_ischeck' => $space_ischeck,
			'gd_ischeck' => $gd_ischeck,
			'gd' => $gd,
			'os' => PHP_OS,
			'version' => phpversion(),
			'mysql' => $mysql,
			'pdo_mysql' => $pdo_mysql_ischeck,
			'upload' => $currentUpload,
			'space' => $space);
	}

	/**
	 * 获取推荐的环境配置信息
	 *
	 * @return array 
	 */
	private function _getRecommendEnvironment() {
		return array(
			'os' => '类UNIX',
			'version' => '>5.3.x',
			'mysql' => '>5.x.x',
			'pdo_mysql' => '必须',
			'upload' => '>2M',
			'space' => '>50M',
			'gd' => '>2.0.28');
	}

	/**
	 * 获取环境的最低配置要求
	 *
	 * @return array
	 */
	private function _getLowestEnvironment() {
		return array(
			'os' => '不限制',
			'version' => '5.1.2',
			'mysql' => '4.2',
			'pdo_mysql' => '必须',
			'upload' => '不限制',
			'space' => '50M',
			'gd' => '2.0');
	}

	/**
	 * 检查目录权限
	 *
	 * @return array
	 */
	private function _checkFileRight() {
		$rootdir = Wind::getRootPath('ROOT');
		
		$files_writeble[] = CONF_PATH;
		$files_writeble[] = DATA_PATH; //数据缓存目录
		$files_writeble[] = DATA_PATH . 'cache/';
		$files_writeble[] = DATA_PATH . 'compile/';
		$files_writeble[] = DATA_PATH . 'log/';
		$files_writeble[] = DATA_PATH . 'tmp/';
		$files_writeble[] = DATA_PATH . 'design/';
		$files_writeble[] = EXT_PATH; //扩展应用目录
		$files_writeble[] = ATTACH_PATH ; //本地附近目录
		$files_writeble[] = HTML_PATH; //本地静态文件可写目录
		$files_writeble[] = THEMES_PATH; //风格目录
		$files_writeble[] = THEMES_PATH . 'extres/';
		$files_writeble[] = THEMES_PATH . 'forum/';
		$files_writeble[] = THEMES_PATH . 'portal/';
		$files_writeble[] = THEMES_PATH . 'site/';
		$files_writeble[] = THEMES_PATH . 'space/';
		$files_writeble[] = PUBLIC_PATH . 'windid/attachment/';
		
		$files_writeble[] = $this->_getDatabaseFile();
		$files_writeble[] = $this->_getFounderFile();
		//$files_writeble[] = $this->_getWindidFile();
		
		$files_writeble = array_unique($files_writeble);
		sort($files_writeble);
		$writable = array();
		foreach ($files_writeble as $file) {
			$key = str_replace($rootdir, '', $file);
			$isWritable = $this->_checkWriteAble($file) ? true : false;
			if ($isWritable) {
				$flag = false;
				foreach ($writable as $k=>$v) {
					if (0 === strpos($key, $k)) $flag = true;
				}
				$flag || $writable[$key] = $isWritable;
			} else {
				$writable[$key] = $isWritable;
			}
		}
		return $writable;
	}

	/**
	 * 检查目录可写
	 *
	 * @param string $pathfile
	 * @return boolean
	 */
	private function _checkWriteAble($pathfile) {
		if (!$pathfile) return false;
		$isDir = in_array(substr($pathfile, -1), array('/', '\\')) ? true : false;
		if ($isDir) {
			if (is_dir($pathfile)) {
				mt_srand((double) microtime() * 1000000);
				$pathfile = $pathfile . 'pw_' . uniqid(mt_rand()) . '.tmp';
			} elseif (@mkdir($pathfile)) {
				return self::_checkWriteAble($pathfile);
			} else {
				return false;
			}
		}
		@chmod($pathfile, 0777);
		$fp = @fopen($pathfile, 'ab');
		if ($fp === false) return false;
		fclose($fp);
		$isDir && @unlink($pathfile);
		return true;
	}

	/**
	 * 创建创始人
	 *
	 * @param string $manager
	 * @param string $manager_pwd
	 * @param string $manager_email
	 * @return PwError
	 */
	private function _writeFounder($manager, $manager_pwd, $manager_email) {
		Wekit::C()->reload('windid');
		Wind::import('SRV:user.dm.PwUserInfoDm');
		$data = array($manager => md5($manager_pwd));
		WindFile::savePhpData($this->_getFounderFile(), $data);
		
		//TODO 创始人添加：用户的配置信息先更新。添加完之后再更新回 开始
		$config = new PwConfigSet('register');
		$config->set('security.username.max', 15)
		->set('security.ban.username', '')
		->set('security.username.min', 1)
		->set('security.password.max', 25)
		->set('security.password.min', 1)
		->flush();
		
		Wind::import('WINDID:service.config.srv.WindidConfigSet');
		$windidConfig = new WindidConfigSet('reg');
		$windidConfig->set('security.ban.username', '')
			->set('security.password.max', 25)
			->set('security.password.min', 1)
			->set('security.username.max', 15)
			->set('security.username.min', 1)
			->flush();
		Wekit::C()->reload('register');
		WindidApi::C()->reload('reg');
		//TODO结束
		$userDm = new PwUserInfoDm();
		$userDm->setUsername($manager)->setPassword($manager_pwd)->setEmail($manager_email)->setGroupid(3)->setRegdate(
			Pw::getTime())->setLastvisit(Pw::getTime())->setRegip(Wind::getApp()->getRequest()->getClientIp())->setGroups(array('3'=>'0'));
		
		//特殊操作  gao.wanggao
		if (true !== ($result = $userDm->beforeAdd())) {
			$this->showError($result->getError());
		}
		if (($uid = Wekit::load('WSRV:user.WindidUser')->addUser($userDm->dm)) < 1) {
			$this->showError('WINDID:code.' . $uid);
		}
		
		$userDm->setUid($uid);
		
		Wind::import('SRV:user.PwUser');
		$daoMap = array();
		$daoMap[PwUser::FETCH_MAIN] = 'user.dao.PwUserDao';
		$daoMap[PwUser::FETCH_DATA] = 'user.dao.PwUserDataDao';
		$daoMap[PwUser::FETCH_INFO] = 'user.dao.PwUserInfoDao';
		Wekit::loadDaoFromMap(PwUser::FETCH_ALL, $daoMap, 'PwUser')->addUser($userDm->getSetData());
		//特殊操作  
			
			
		//$uid = Wekit::load('user.PwUser')->addUser($userDm);
		//TODO 创始人添加完成：恢复默认数据：开始
		$config = new PwConfigSet('register');
		$config->set('security.username.max', 15)
		->set('security.ban.username', '创始人,管理员,版主,斑竹,admin')
		->set('security.username.min', 3)
		->set('security.password.max', 15)
		->set('security.password.min', 6)
		->flush();
		

		$windidConfig = new WindidConfigSet('reg');
		$windidConfig->set('security.ban.username', '创始人,管理员,版主,斑竹,admin')
			->set('security.password.max', 15)
			->set('security.password.min', 6)
			->set('security.username.max', 15)
			->set('security.username.min', 3)
			->flush();
		//TODO 结束
		
		if ($uid instanceof PwError) {
			$this->showError($uid->getError());
		}
		Wekit::load('user.PwUserBelong')->update($uid, array(3 => 0));
		
		//特殊操作  gao.wanggao
        $this->_defaultAvatar($uid);
        $this->_defaultAvatar(0);
        //特殊操作  
		//Wekit::load('user.srv.PwUserService')->restoreDefualtAvatar($uid);//用户的默认头像需要设置
		//Wekit::load('user.srv.PwUserService')->restoreDefualtAvatar(0);//游客的默认头像需要设置
		
		//站点统计信息
		Wind::import('SRV:site.dm.PwBbsinfoDm');
		$dm = new PwBbsinfoDm();
		$dm->setNewmember($manager)->addTotalmember(1);
		Wekit::load('site.PwBbsinfo')->updateInfo($dm);
		return $uid;
	}
	
	private function _defaultAvatar($uid, $type = 'face') {
		Wind::import('LIB:upload.PwUpload');
		$_avatar = array('.jpg' => '_big.jpg', '_middle.jpg' => '_middle.jpg', '_small.jpg' => '_small.jpg');
		$defaultBanDir = Wind::getRealDir('ROOT:')  . 'res/images/face/';
		$fileDir =  'avatar/' . Pw::getUserDir($uid) . '/';
		$attachPath = Wind::getRealDir('ROOT:') . 'windid/attachment/';
		foreach ($_avatar as $des => $org) {
			$toPath = $attachPath . $fileDir . $uid . $des;
			$fromPath = $defaultBanDir . $type . $org;
			PwUpload::createFolder(dirname($toPath));
			PwUpload::copyFile($fromPath, $toPath);
		}
		return true;
	}
	
	private function _writeWindid() {
		$baseUrl = Wekit::url()->base;
		$key = md5(WindUtility::generateRandStr(10));
		$charset = Wekit::V('charset');
		$charset = str_replace('-', '', strtolower($charset));
		if (!in_array($charset, array('gbk', 'utf8', 'big5'))) $charset = 'utf8';
		
		$config = new PwConfigSet('windid');
		$config->set('windid', 'local')
		->set('serverUrl', $baseUrl . '/windid')
		->set('clientId', 1)
		->set('clientKey', $key)
		->set('connect', 'db')->flush();
		Wekit::C()->reload('windid');
		
		Wind::import('WINDID:service.app.dm.WindidAppDm');
		$dm = new WindidAppDm();
		$dm->setApiFile('windid.php')
			->setIsNotify('1')
			->setIsSyn('1')
			->setAppName('phpwind9.0')
			->setSecretkey($key)
			->setAppUrl($baseUrl)
			->setCharset($charset)
			->setAppIp('');
		$result = WindidApi::api('app')->addApp($dm);
		if ($result instanceof WindidError) $this->showError('INSTALL:windid.init.fail');
		WindidApi::api('avatar')->setStorages('local');
		Wekit::load('config.PwConfig')->setConfig('site', 'avatarUrl', $baseUrl . '/windid/attachment');
		Wind::import('WINDID:service.config.srv.WindidConfigSet');
		$windidConfig = new WindidConfigSet('site');
		$windidConfig->set('avatarUrl', $baseUrl . '/windid/attachment')->flush();
		return true;
	}

	/**
	 * 检查目录
	 *
	 * @return array
	 */
	private function _checkDatabase() {
		if (!WindFile::isFile($this->_getDatabaseFile()) || !WindFile::isFile($this->_getTableSqlFile())) {
			$this->showError('INSTALL:database_config_noexists');
		}
		if (!$this->_checkWriteAble($this->_getDatabaseFile())) {
			$this->showError('INSTALL:error_777_database');
		}
		if (!$this->_checkWriteAble($this->_getFounderFile())) {
			$this->showError('INSTALL:error_777_founder');
		}
		
		/*if (!$this->_checkWriteAble($this->_getWindidFile())) {
			$this->showError('INSTALL:error_777_windid');
		}*/
		
		$database = include $this->_getTempFile();
		if (!$database['founder']) {
			$this->showError('INSTALL:database_config_error');
		}
		return $database;
	}

	private function _getDemoThreads() {
		$data = include Wind::getRealPath("APPS:install.lang.demo_threads");
		return $data;
	}
	
	/*private function _getWindidFile() {
		return Wind::getRealPath('ROOT:conf.windidconfig.php', true);
	}*/

	private function _getFounderFile() {
		return Wind::getRealPath('ROOT:conf.founder.php', true);
	}

	private function _getInstallLockFile() {
		return Wind::getRealPath('DATA:install.lock', true);
	}

	private function _getTableSqlFile() {
		return Wind::getRealPath('DATA:tmp.install.sql', true);
	}

	private function _getTableLogFile() {
		return Wind::getRealPath('DATA:tmp.install.log', true);
	}

	private function _getTempFile() {
		return Wind::getRealPath('DATA:tmp.database.php', true);
	}
	
	private function _getDatabaseFile() {
		return Wind::getRealPath('ROOT:conf.database.php', true);
	}
}
