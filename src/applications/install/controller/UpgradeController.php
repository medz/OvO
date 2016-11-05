<?php
define('WIND_SETUP', 'update');
define('NEXT_VERSION', '9.0');

/**
 * 87to90升级流程
 *
 * @author xiaoxia.xu<xiaoxia.xuxx@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: UpgradeController.php 24779 2013-02-21 06:24:27Z xiaoxia.xuxx $
 * @package applications.install.controller
 */
class UpgradeController extends WindController {
	private $_tmpconfig = array();
	
	/* (non-PHPdoc)
	 * @see WindSimpleController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {

		$this->setOutput(NEXT_VERSION, 'wind_version');
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
		$this->setOutput('phpwind 8.7 to 9.0', 'wind_version');
		
		//ajax递交编码转换
		$token = $this->getInput('token', 'get');
		$lockFile = Wind::getRealPath('DATA:setup.setup.lock', true);
		if (file_exists($lockFile) && !$token) {
			$this->showError('升级程序已被锁定, 如需重新运行，请先删除setup.lock');
		}
		$encryptToken = trim(file_get_contents($lockFile));
		if (md5($token) != $encryptToken) {
			$this->showError('升级程序访问异常! 重新安装请先删除setup.lock');
		}
	}

	/**
	 * 87升级到9更新缓存
	 */
	public function run() {
		//$db = $this->_checkDatabase();
		Wekit::createapp('phpwind');
		//更新HOOK配置数据
		Wekit::load('hook.srv.PwHookRefresh')->refresh();
		
		//初始化站点config
		$site_hash = WindUtility::generateRandStr(8);
		$cookie_pre = WindUtility::generateRandStr(3);
		Wekit::load('config.PwConfig')->setConfig('site', 'hash', $site_hash);
		Wekit::load('config.PwConfig')->setConfig('site', 'cookie.pre', $cookie_pre);
		Wekit::load('config.PwConfig')->setConfig('site', 'info.url', PUBLIC_URL);
		Wekit::load('nav.srv.PwNavService')->updateConfig();
		
		//风格默认数据
		Wekit::load('APPCENTER:service.srv.PwStyleInit')->init();
		
		//计划任务默认数据
		Wekit::load('cron.srv.PwCronService')->updateSysCron();
		//版块的统计数据更新
		/* @var $forumMisc PwForumMiscService */
		$forumMisc = Wekit::load('forum.srv.PwForumMiscService');
		$forumMisc->correctData();
		$forumMisc->countAllForumStatistics();
		
		//更新数据缓存
		/* @var $usergroup PwUserGroupsService */
		$usergroup = Wekit::load('SRV:usergroup.srv.PwUserGroupsService');
		$usergroup->updateLevelCache();
		$usergroup->updateGroupCache();
		$usergroup->updateGroupRightCache();
		/* @var $emotion PwEmotionService */
		$emotion = Wekit::load('SRV:emotion.srv.PwEmotionService');
		$emotion->updateCache();

		//门户演示数据
		Wekit::load('SRV:design.srv.PwDesignDefaultService')->likeModule();
		Wekit::load('SRV:design.srv.PwDesignDefaultService')->tagModule();
		Wekit::load('SRV:design.srv.PwDesignDefaultService')->reviseDefaultData();
		
		//全局缓存更新
		Wekit::load('SRV:cache.srv.PwCacheUpdateService')->updateConfig();
		Wekit::load('SRV:cache.srv.PwCacheUpdateService')->updateMedal();
		$this->_writeWindid();
		$this->_designUpgrade();
		//清理升级过程的文件
//		WindFile::del(Wind::getRealPath('DATA:setup.setup_config.php', true));
// 		WindFile::del(Wind::getRealPath('DATA:setup.tmp_dbsql.php', true));
		$this->setTemplate('upgrade_finish');
	}

	/**
	 * 头像转移
	 */
	public function avatarAction() {
		$end_uid = $this->getMaxUid();
		ini_set('max_execution_time', 0);
		$time_start = microtime(true);

		list($ftp, $attachDir) = $this->_getFtp();
		$defauleDir = rtrim(Wind::getRealDir('PUBLIC:res.images.face', true), '/');
		
		list($start_uid, $end) = $this->_getStartAndLimit(intval($this->getInput('uid', 'get')), $end_uid, $ftp ? true : false);
		while ($start_uid < $end) {
			$res = $this->_getOldAvatarPath($attachDir, $start_uid);
			$big = $res['big'];
			$middle = $res['middle'];
			$small = $res['small'];
			if (!$this->checkFile($middle)) {
				$big = $defauleDir . '/face_big.jpg';
				$middle = $defauleDir . '/face_middle.jpg';
				$small = $defauleDir . '/face_small.jpg';
			}
			$_toPath = '/avatar/' . Pw::getUserDir($start_uid) . '/';
			$to_big = $_toPath . $start_uid . '.jpg';
			$to_middle = $_toPath . $start_uid . '_middle.jpg';
			$to_small = $_toPath . $start_uid . '_small.jpg';
			
			if ($ftp) {
				$ftp->mkdirs($_toPath);
				$ftp->upload($big, $to_big);
				$ftp->upload($middle, $to_middle);
				$ftp->upload($small, $to_small);
			} else {
				WindFolder::mkRecur($attachDir . $_toPath);
				copy($big, $attachDir . $to_big);
				copy($middle, $attachDir . $to_middle);
				copy($small, $attachDir . $to_small);
			}
			$start_uid++;
		}
		if ($end < $end_uid) {
			$this->setOutput($end, 'uid');
			$this->setOutput($this->getInput('token', 'get'), 'token');
			$this->setTemplate('upgrade_avatar');
		} else {
			$this->showMessage('升级成功！');
		}
	}
	
	/**
	 * 获得ftp对象
	 *
	 * @return WindSocketFtp|NULL
	 */
	private function _getFtp() {
		$db_ftp = $this->_getConfig('db_ftp');
		$ftp = $attachDir = null;
		if ($db_ftp['db_ifftp']) {
			Wind::import('WIND:ftp.WindSocketFtp');
			$ftp = new WindSocketFtp(array(
				'server' => $db_ftp['ftp_server'],
				'port' => $db_ftp['ftp_port'],
				'user' => $db_ftp['ftp_user'],
				'pwd' => $db_ftp['ftp_pass'],
				'dir' => $db_ftp['ftp_dir'],
				'timeout' => $db_ftp['ftp_timeout'],
			));
			$attachDir = $db_ftp['db_ftpweb'];
		} else {
			//头像放到www/windid/attachment下
			$attachDir = rtrim(Wind::getRealDir('PUBLIC:', true), '/');
			$attachDir .= '/windid/attachment';
		}
		$attachDir = rtrim($attachDir, '/');
		return array($ftp, $attachDir);
	}
	
	/**
	 * 获得开始结束的ID
	 *
	 * @param int $start
	 * @param int $max
	 * @param boolean $isFtp
	 * @return array
	 */
	private function _getStartAndLimit($start_uid, $end_uid, $isFtp = true) {
		$limit = 10;
		if (!$isFtp) {
			$limit = 200;
			/* $_lt = $this->_getConfig('limit');
			$_lt = is_numeric($_lt) ? abs($_lt) : 1;
			$_lt == 0 && $_lt = 1;
			$limit = 1000 * $_lt; */
		}
		
		if ($start_uid < 1) $start_uid = 1;
		if ($start_uid >= $end_uid) {
			$this->showMessage('头像升级成功');
		}
		$end = ($start_uid + $limit) > $end_uid ? $end_uid : ($start_uid + $limit);
		return array($start_uid, $end);
	}
	
	/**
	 * 检查文件
	 *
	 * @param string $filename
	 * @return boolean
	 */
	private function checkFile($filename) {
		if (!@fopen($filename, 'r')) return false;
		return true;
		// $res = get_headers($file);
		// $r = trim($res['0']);
		// if(strpos($r,'OK')) return true;
		// return false;
	}

	/**
	 * 获取ftp配置文件
	 *
	 * @return array
	 */
	private function _getConfig($name = '') {
		if ($this->_tmpconfig) return isset($this->_tmpconfig[$name]) ? $this->_tmpconfig[$name] : $this->_tmpconfig;
		$file = Wind::getRealPath('DATA:setup.setup_config.php', true);
		if (!is_file($file)) {
			$this->showError('配置文件不存在');
		}
		require $file;
		$this->_tmpconfig = $setupConfig;
		return isset($this->_tmpconfig[$name]) ? $this->_tmpconfig[$name] : $this->_tmpconfig;
	}

	/**
	 * 获得用户87中的头像
	 *
	 * @param string $attachDir
	 * @param int $tempuid
	 * @return string
	 */
	private function _getOldAvatarPath($attachDir, $tempuid) {
		$udir = str_pad(substr($tempuid, -2), 2, '0', STR_PAD_LEFT);
		$user_a = $udir . '/' . $tempuid . '.jpg';
		$faceurl = array();
		$faceurl['middle'] = $attachDir . "/upload/middle/$user_a";
		$faceurl['big'] = $attachDir . "/upload/middle/$user_a";
		$faceurl['small'] = $attachDir . "/upload/small/$user_a";
		return $faceurl;
	}

	/**
	 * 获得最大的用户ID
	 *
	 * @return int
	 */
	private function getMaxUid() {
		$db_config = $this->_getConfig('db_config');
		$link = mysql_connect(sprintf("%s:%s", $db_config['src_host'], $db_config['src_port']), $db_config['src_username'], $db_config['src_password'], true);
		$pre = $db_config['src_dbpre'] ? $db_config['src_dbpre'] : 'pw_';
		if (!$link) {
			$this->showError("Access denied for user '{$db_config['src_username']}'@'{$db_config['src_host']}' (using password: YES)");
		}
		$rt = mysql_select_db($db_config['src_dbname'], $link);
		if (false === $rt) {
			$this->showError('SQL ERROR:' . mysql_error($link));
		}
		$sql = sprintf("SELECT MAX(uid) FROM %smembers", trim($pre));
		$rt = mysql_query($sql, $link);
		if (false === $rt) {
			$this->showError('SQL ERROR:' . mysql_error($link) . ' IN "' . $sql . '"');
		}
		$result = mysql_fetch_array($rt, MYSQL_NUM);
		if (!$result[0]) {
			$this->showMessage('没有用户头像需要转换');
		}
		return $result[0];
	}
	
	/**
	 * windid更新
	 * 
	 * @return boolean
	 */
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
		$service = WindidApi::api('app');
		$result = $service->addApp($dm);
		if ($result instanceof WindidError) $this->showError('INSTALL:windid.init.fail');
		return true;
	}
	/**
	 * 自定义页面升级  start
	 * 
	 * @return boolean
	 */
	protected function _designUpgrade() {
		Wind::import('SRV:design.srv.vo.PwDesignPortalSo');
		$vo = new PwDesignPortalSo();
		$vo->setIsopen(1);
		$list = $this->_getPortalDs()->searchPortal($vo, 0, 100);
		$dirList = array();
		foreach ($list AS $k=>$v) {
			if(empty($v['template']))  $dirList[$k] = $v['id'];
		}
		
		
		$dir = Wind::getRealDir('THEMES:portal.local.');
		$_dir = array();
		if (!is_dir($dir)) return array();
		if (!$handle = @opendir($dir)) return array();
		while (false !== ($file = @readdir($handle))) {
			if ('.' === $file || '..' === $file) continue;
			$fileName = $dir . $file;
			if (is_file($fileName)){
				continue;
			}elseif (is_dir($fileName) && is_numeric($file)) {
				$key = array_search($file, $dirList);
				unset($dirList[$k]);
				if ((int)$file != $file) continue;
				$tplPath = 'special_'.$file;
				Wind::import('SRV:design.dm.PwDesignPortalDm');
				$dm = new PwDesignPortalDm($file);
			    $dm->setTemplate($tplPath);
			    Wekit::load('design.PwDesignPortal')->updatePortal($dm);
				$this->copyRecur($fileName, $dir . $tplPath . '/');
			}
		}
		$srv = Wekit::load('design.srv.PwDesignService');
	
		foreach ($dirList AS $k=>$v) {
			$tplPath = 'special_'.$v;
			$result = $srv->defaultTemplate($k, $tplPath);
			if ($result) {
				WindFile::write($dir . $tplPath . '/template/index.htm', $this->_tpl());
				Wind::import('SRV:design.dm.PwDesignPortalDm');
				$dm = new PwDesignPortalDm($v);
			    $dm->setTemplate($tplPath);
			    Wekit::load('design.PwDesignPortal')->updatePortal($dm);
			}
		}
		@closedir($handle);
		return true;
	}
	
	/**
	 * 复制目录
	 *
	 * @param string $fromFolder
	 * @param string $toFolder
	 * @return boolean
	 */
	protected function copyRecur($fromFolder, $toFolder) {
	    $dir = @opendir($fromFolder);
	    if (!$dir) return false;
	    WindFolder::mk($toFolder);
	    while(false !== ($file = readdir($dir)) ) {
	        if (($file != '.' ) && ($file != '..' )) {
	            if (is_dir($fromFolder . '/' . $file) ) {
	               $this->copyRecur($fromFolder . '/' . $file, $toFolder . '/' . $file);
	            }else {
	                @copy($fromFolder . '/' . $file, $toFolder . '/' . $file);
	                @chmod($toFolder . '/' . $file, 0777);
	            }
	        }
	    }
	    @closedir($dir);
	    return true;
	}
	
	private function _getPortalDs() {
		return Wekit::load('design.PwDesignPortal');
	}
	
	private function _tpl() {
		return  <<<TPL
<!doctype html>
<html>
<head>
<template source='TPL:common.head' load='true' />
</head>
<body>
<design role="start"/>
	<!--# 
	\$wrapall = !\$portal['header'] ? 'custom_wrap' : 'wrap';
	#-->
	<div class="{\$wrapall}">
	<!--# if(\$portal['header']){ #-->
	<template source='TPL:common.header' load='true' />
	<!--# } #-->
	<div class="main_wrap">
	<!--# if(\$portal['navigate']){ #-->
		<div class="bread_crumb">{@\$headguide|html}</div>
	<!--# } #-->
		<div class="main cc">
			<design role="tips" id="nodesign"/>
			<design role="segment" id="segment1"/>
		</div>
	</div>
	<!--# if(\$portal['footer']){ #-->
	<template source='TPL:common.footer' load='true' />
	<!--# } #-->
	</div>
<script>
Wind.use('jquery', 'global');
</script>
<design role="end"/>
</body>
</html>
TPL;
		
	}
	/**
	 * 自定义页面升级 end
	 */

	
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
// 		$referer && $referer = WindUrlHelper::createUrl($referer);
		$this->addMessage('up87to90.php?step=init&action=end&seprator=1&token=' . $this->getInput('token', 'get'), 'referer');
		$this->addMessage($refresh, 'refresh');
		if ($lang) {
			$lang = Wind::getComponent('i18n');
			$error = $lang->getMessage($error);
		}
		parent::showMessage($error);
	}
}