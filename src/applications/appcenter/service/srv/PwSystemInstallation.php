<?php
Wind::import('APPCENTER:service.srv.PwInstallApplication');
Wind::import('APPCENTER:service.srv.helper.PwFtpSave');
Wind::import('APPCENTER:service.srv.helper.PwSftpSave');
/**
 * 系统升级服务
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwSystemInstallation.php 24976 2013-02-27 09:29:00Z long.shi $
 * @package wind
 */
class PwSystemInstallation extends PwInstallApplication {
	protected $local;
	public $target;
	protected $tmpPath = 'DATA:upgrade';
	public $useZip = 1;

	public function __construct() {
		$this->local = 'phpwind_' . str_replace('.', '', NEXT_VERSION) . '_' . NEXT_RELEASE;
		$this->tmpPath = Wind::getRealDir($this->tmpPath, true);
		WindFolder::mkRecur($this->tmpPackage);
	}

	/**
	 * 根据当前版本+release获取升级信息(patch包地址) ,hash值
	 * 获取升级文件列表
	 */
	public function checkUpgrade() {
		$url = PwApplicationHelper::acloudUrl(
			array('a' => 'forward', 'do' => 'getUpdateInfo', 'pwversion' => $this->local));
		
		$r = PwApplicationHelper::requestAcloudData($url);
		$r = WindConvert::convert($r, Wekit::V('charset'), 'utf8');
		return is_array($r) ? ($r['code'] === '0' ? $r['info'] : $r['msg']) : '无法连接云平台!';
	}

	public function getNotice($loginUser) {
		$notice = 0;
		$upgradeInfo = $patchInfo = array();
		if (Wekit::load('ADMIN:service.srv.AdminFounderService')->isFounder($loginUser->username)) {
			$upgradeInfo = $this->checkUpgrade();
			$upgradeInfo && is_array($upgradeInfo) && $notice |= 1;
			$patchInfo = Wekit::load('APPCENTER:service.srv.PwPatchUpdate')->checkUpgrade();
			$patchInfo && is_array($patchInfo) && $notice |= 2;
		}
		$url = '';
		$f = '<a data-level="2" data-parent="%s" data-id="%s" href="%s" class="J_tabframe_trigger">立即升级</a>';
		switch ($notice) {
			case 1:
				$notice = '您正在使用旧版本的phpwind，为了获得更好的体验，请升级至最新版。';
				$url = WindUrlHelper::createUrl('appcenter/upgrade/run');
				break;
			case 2:
				$notice = '您正在使用的版本有更新补丁，请安装补丁。';
				$url = WindUrlHelper::createUrl('appcenter/fixup/run');
				break;
			case 3:
				$notice = '您正在使用的版本有新增升级包和更新补丁。';
				$url = WindUrlHelper::createUrl('appcenter/upgrade/run');
				break;
			default:
				$notice = '';
		}
		return array('notice' => $notice . ($url ? sprintf($f, 'platform', 'platform_upgrade', $url) : ''), 'info' => array($patchInfo, $upgradeInfo));
	}

	/**
	 * 检查环境
	 *
	 * @return PwError boolean
	 */
	public function checkEnvironment() {
		if (!function_exists('curl_init')) return new PwError('APPCENTER:upgrade.curl');
		// if (!function_exists('gzinflate')) return new
		// PwError('APPCENTER:upgrade.gzinflate');
		return true;
	}

	/**
	 * 下载
	 * 1、下载zip包
	 * 2、单个文件下载
	 */
	public function download($downloadUrl, $hash, $file = '') {
		if ($this->useZip) {
			list($bool, $package) = PwSystemHelper::download($downloadUrl, 
				$this->tmpPath . '/' . basename($downloadUrl));
			if (!$bool) return new PwError($package);
			if ($hash !== md5_file($package)) {
				$this->_log(
					'check Package fail. expected hash:' . $hash . 'real hash :' . md5_file(
						$package));
				return new PwError('APPCENTER:install.checkpackage.fail');
			}
			$this->_log('download zip success, dir:' . $package);
			$this->tmpPackage = $this->tmpPath . DIRECTORY_SEPARATOR . $this->target;
			$r = PwApplicationHelper::extract($package, $this->tmpPath);
			if ($r === false) {
				$this->_log('extract Package fail. ');
				return new PwError('APPCENTER:install.checkpackage.format.fail', 
					array('{{error}}' => $r));
			}
			if ($r != realpath($this->tmpPackage)) {
				PwApplicationHelper::copyRecursive($r, $this->tmpPackage);
			}
			$this->_log('extract zip success, dir:' . $this->tmpPackage);
			return true;
		} else {
			$_file = $this->tmpPath . DIRECTORY_SEPARATOR . $this->target . DIRECTORY_SEPARATOR . $file;
			$dir = dirname($_file);
			list($bool, $_file) = PwSystemHelper::download($downloadUrl . '/' . $file . 'wind', 
				$_file);
			if (!$bool) return new PwError($_file);
			if ($hash !== md5_file($_file)) {
				$this->_log(
					'check file fail. expected hash:' . $hash . 'real hash :' . md5_file($_file));
				return new PwError('APPCENTER:upgrade.check.file.fail');
			}
			$this->_log('download file success, file:' . $_file);
			return true;
		}
	}

	/**
	 * 获取文件转移方式
	 *
	 * @return multitype:Ambigous <string, multitype:>
	 */
	public function getMoveWay() {
		$directory = array(
			'ROOT' => '..', 
			'CONF' => '../conf', 
			'DATA' => '../data', 
			'SRC' => '../src', 
			'APPS' => '../src/applications', 
			'EXT' => '../src/extensions', 
			'HOOK' => '../src/hooks', 
			'LIB' => '../src/library', 
			'SRV' => '../src/service', 
			'REP' => '../src/repository', 
			'WINDID' => '../src/windid', 
			'ACLOUD' => '../src/aCloud', 
			'PUBLIC' => '../www', 
			'THEMES' => '../www/themes', 
			'TPL' => '../www/template', 
			'ATTACH' => '../www/attachment', 
			'HTML' => '../www/html');
		$sort = array(
			'TPL', 
			'THEMES', 
			'ACLOUD', 
			'WINDID', 
			'REP', 
			'SRV', 
			'LIB', 
			'HOOK', 
			'EXT', 
			'APPS', 
			'CONF', 
			'DATA', 
			'SRC', 
			'PUBLIC');
		$strtr = array();
		$localDirectory = @include Wind::getRealPath('CONF:directory.php', true);
		foreach ($sort as $v) {
			if ($directory[$v] == $localDirectory[$v]) continue;
			$search = PwSystemHelper::relative(
				WEKIT_PATH . str_replace('/', DIRECTORY_SEPARATOR, $directory[$v]));
			$strtr[rtrim($search, '/\\')] = rtrim(Wind::getRootPath($v), '/\\');
		}
		return $strtr;
	}

	/**
	 * 根据现有目录结构调整升级包目录
	 *
	 * @param array $fileList        	
	 */
	public function sortDirectory($fileList) {
		$this->_log('start to sort the directory');
		$sourceDir = $this->tmpPath . DIRECTORY_SEPARATOR . $this->target;
		$directoryFile = $sourceDir . DIRECTORY_SEPARATOR . 'conf/directory.php';
		$directory = @include $directoryFile;
		if (!is_array($directory)) {
			return new PwError('APPCENTER:upgrade.directory.fail');
		}
		$this->_log('the remote directory is:' . var_export($directory, true));
		$root = Wind::getRealDir('ROOT:');
		
		$sort = array(
			'TPL', 
			'THEMES', 
			'ACLOUD', 
			'WINDID', 
			'REP', 
			'SRV', 
			'LIB', 
			'HOOK', 
			'EXT', 
			'APPS', 
			'CONF', 
			'DATA', 
			'SRC', 
			'PUBLIC');
		$strtr = array();
		$localDirectory = @include Wind::getRealPath('CONF:directory.php', true);
		foreach ($sort as $v) {
			if ($directory[$v] == $localDirectory[$v]) continue;
			$search = PwSystemHelper::relative(
				WEKIT_PATH . str_replace('/', DIRECTORY_SEPARATOR, $directory[$v]));
			$strtr[rtrim($search, '/\\')] = rtrim(Wind::getRootPath($v), '/\\');
		}
		$this->_log('way of moving directory' . var_export($strtr, true));
		
		if (!$sourceMd5 = WindFile::read($sourceDir . DIRECTORY_SEPARATOR . 'conf/md5sum')) {
			return new PwError('APPCENTER:upgrade.target.hash.fail');
		}
		$sourceMd5 = PwSystemHelper::resolveMd5($sourceMd5);
		// md5sum
		$data = '';
		foreach ($sourceMd5 as $v => $md5) {
			$v = trim($v, '/');
			$v = str_replace('/', DIRECTORY_SEPARATOR, $v);
			$_v = ROOT_PATH . $v;
			$file = $v;
			foreach ($strtr as $search => $replace) {
				if (0 === strpos($_v, $search)) {
					$file = str_replace(ROOT_PATH, '', $replace . substr($_v, strlen($search)));
					$file = str_replace('//', '/', $file);
					break;
				}
			}
			$data .= PwSystemHelper::md5content($md5, $file);
		}
		WindFile::write($sourceDir . DIRECTORY_SEPARATOR . 'conf/md5sum', $data);
		
		$moveList = $newFileList = array();
		
		if (!$tmp = WindFile::read(CONF_PATH . 'md5sum')) {
			return new PwError('APPCENTER:upgrade.hash.fail');
		}
		$md5List = PwSystemHelper::resolveMd5($tmp);
		$this->_log('obtain the md5 list of current version');
		
		foreach ($fileList as $v) {
			$v = trim($v, '/');
			$v = str_replace('/', DIRECTORY_SEPARATOR, $v);
			$_v = $root . $v;
			$file = $v;
			foreach ($strtr as $search => $replace) {
				if (0 === strpos($_v, $search)) {
					$file = str_replace($root, '', $replace . substr($_v, strlen($search)));
					$file = str_replace('//', '/', $file);
					$moveList[$v] = $file;
					break;
				}
			}
			$newFileList[$file] = $md5List[str_replace(DIRECTORY_SEPARATOR, '/', $file)];
		}
		$this->_log('files need to move ' . var_export($moveList, true));
		$relativePath_1 = PwSystemHelper::resolveRelativePath(PUBLIC_PATH, 
			Wind::getRealPath('SRC:wekit'));
		$relativePath_2 = PwSystemHelper::resolveRelativePath(PUBLIC_PATH . 'aCloud', 
			Wind::getRealPath('SRC:wekit'));
		foreach ($moveList as $old => $new) {
			WindFolder::mkRecur(dirname($sourceDir . DIRECTORY_SEPARATOR . $new));
			copy($sourceDir . DIRECTORY_SEPARATOR . $old, $sourceDir . DIRECTORY_SEPARATOR . $new);
			WindFile::del($sourceDir . DIRECTORY_SEPARATOR . $old);
			if ('.php' === substr($old, -4) && !strncasecmp($old, 'www' . DIRECTORY_SEPARATOR, 4)) {
				$content = WindFile::read($sourceDir . DIRECTORY_SEPARATOR . $new);
				if (strpos($content, '../../src/wekit.php')) {
					$content = str_replace('../../src/wekit.php', $relativePath_2, $content);
				} else {
					$content = str_replace('../src/wekit.php', $relativePath_1, $content);
				}
				WindFile::write($sourceDir . DIRECTORY_SEPARATOR . $new, $content);
			}
		}
		// 目录文件
		if ($directory != $localDirectory) {
			$directory = array_merge($directory, $localDirectory);
			$temp = "<?php\r\n defined('WEKIT_VERSION') or exit(403);\r\n return ";
			$temp .= WindString::varToString($directory) . ";\r\n?>";
			WindFile::write($directoryFile, $temp);
		}
		Wekit::cache()->set('system_upgrade_replace', 1);
		return $newFileList;
	}

	/**
	 * 补救措施，临时解决方案，后续版本@deprecated
	 * 
	 * @param unknown_type $fileList
	 * @param unknown_type $useFtp
	 * @param unknown_type $oldList
	 * @return boolean|PwError
	 */
	public function after($fileList, $useFtp, $oldList) {
		if (Wekit::cache()->get('system_upgrade_replace')) return true;
		$relativePath_1 = PwSystemHelper::resolveRelativePath(PUBLIC_PATH, 
			Wind::getRealPath('SRC:wekit'));
		$relativePath_2 = PwSystemHelper::resolveRelativePath(PUBLIC_PATH . 'aCloud', 
			Wind::getRealPath('SRC:wekit'));
		$strtr = $this->getMoveWay();
		$move = array();
		$entrance = array('index.php', 'read.php', 'install.php', 'windid.php', 'admin.php', 'alipay.php', 'pay99bill.php', 'paypal.php', 'tenpay.php');
		
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			foreach ($oldList as $v) {
				$v = trim($v, '/');
				$v = str_replace('/', DIRECTORY_SEPARATOR, $v);
				$_v = ROOT_PATH . $v;
				$file = $v;
				foreach ($strtr as $search => $replace) {
					if (0 === strpos($_v, $search)) {
						$file = str_replace(ROOT_PATH, '', $replace . substr($_v, strlen($search)));
						$file = str_replace('//', '/', $file);
						$move[$file] = $_v;
						break;
					}
				}
				$baseFile = basename($_v);
				if ('.php' === substr($baseFile, -4) && (in_array($baseFile, $entrance) || !strncasecmp($baseFile, 'update_' . DIRECTORY_SEPARATOR, 4))) {
					$content = WindFile::read($_v);
					if (strpos($content, '../../src/wekit.php')) {
						$content = str_replace('../../src/wekit.php', $relativePath_2, $content);
					} else {
						$content = str_replace('../src/wekit.php', $relativePath_1, $content);
					}
					$tmp = tempnam($this->tmpPath, basename($_v) . WindUtility::generateRandStr(3));
					WindFile::write($tmp, $content);
					$move[$file] = $tmp;
				}
			}
		} else {
			foreach ($fileList as $f => $hash) {
				$_v = ROOT_PATH . $f;
				$baseFile = basename($_v);
				if ('.php' === substr($baseFile, -4) && (in_array($baseFile, $entrance) || !strncasecmp($baseFile, 'update_' . DIRECTORY_SEPARATOR, 4))) {
					$content = WindFile::read($_v);
					if (strpos($content, '../../src/wekit.php')) {
						$content = str_replace('../../src/wekit.php', $relativePath_2, $content);
					} else {
						$content = str_replace('../src/wekit.php', $relativePath_1, $content);
					}
					$tmp = tempnam($this->tmpPath, basename($_v) . WindUtility::generateRandStr(3));
					WindFile::write($tmp, $content);
					$move[$f] = $tmp;
				}
			}
		}
		
		// MD5SUM
		$md5File = WindFile::read(CONF_PATH . 'md5sum');
		$sourceMd5 = PwSystemHelper::resolveMd5($md5File);
		$data = '';
		foreach ($sourceMd5 as $v => $md5) {
			$v = trim($v, '/');
			$v = str_replace('/', DIRECTORY_SEPARATOR, $v);
			$_v = ROOT_PATH . $v;
			$file = $v;
			foreach ($strtr as $search => $replace) {
				if (0 === strpos($_v, $search)) {
					$file = str_replace(ROOT_PATH, '', $replace . substr($_v, strlen($search)));
					$file = str_replace('//', '/', $file);
					break;
				}
			}
			$data .= PwSystemHelper::md5content($md5, $file);
		}
		$tmp = tempnam($this->tmpPath, 'md5temp');
		WindFile::write($tmp, $data);
		$move[str_replace(ROOT_PATH, '', CONF_PATH . 'md5sum')] = $tmp;
		// 入口文件
		if ($useFtp) {
			try {
				$ftp = $useFtp['sftp'] ? new PwSftpSave($useFtp) : new PwFtpSave($useFtp);
			} catch (WindFtpException $e) {
				return new PwError(array('APPCENTER:upgrade.ftp.fail', array($e->getMessage())));
			}
		}
		foreach ($move as $k => $v) {
			if ($useFtp) {
				try {
					$r = $ftp->upload($v, $k);
					if ($useFtp['sftp'] && !$r && $e = $ftp->getError()) {
						return new PwError('APPCENTER:upgrade.upload.fail', 
							array($v . var_export($e, true)));
					}
				} catch (WindFtpException $e) {
					return new PwError(array('APPCENTER:upgrade.ftp.fail', array($e->getMessage())));
				}
			} else {
				WindFolder::mkRecur(dirname(ROOT_PATH . $k));
				copy($v, ROOT_PATH . $k);
			}
		}
		$useFtp && $ftp->close();
		return true;
	}

	/**
	 * 验证本地文件
	 *
	 * @param array $fileList        	
	 * @return array
	 */
	public function validateLocalFiles($fileList) {
		$this->_log('start to validate local files - ' . count($fileList));
		list($change, $unchange, $new) = PwSystemHelper::validateMd5($fileList);
		$source = DATA_PATH . 'upgrade' . DIRECTORY_SEPARATOR . $this->target . DIRECTORY_SEPARATOR;
		foreach ($change as $k => $v) {
			if (md5_file($source . $v) == md5_file(ROOT_PATH . $v)) {
				unset($change[$k]);
				$unchange[] = $v;
			}
		}
		return array($change, $unchange, $new);
	}

	/**
	 * 进行升级
	 */
	public function doUpgrade($fileList, $useFtp = 0) {
		$source = Wind::getRealDir('DATA:upgrade') . DIRECTORY_SEPARATOR . $this->target;
		if ($useFtp) {
			try {
				$ftp = $useFtp['sftp'] ? new PwSftpSave($useFtp) : new PwFtpSave($useFtp);
			} catch (WindFtpException $e) {
				return new PwError(array('APPCENTER:upgrade.ftp.fail', array($e->getMessage())));
			}
		}
		
		foreach ($fileList as $v => $hash) {
			if (!is_file($source . DIRECTORY_SEPARATOR . $v)) {
				return new PwError('APPCENTER:upgrade.file.lost');
			}
			if ($useFtp) {
				try {
					$r = $ftp->upload($source . DIRECTORY_SEPARATOR . $v, $v);
					if ($useFtp['sftp'] && !$r && $e = $ftp->getError()) {
						return new PwError('APPCENTER:upgrade.upload.fail', 
							array($v . var_export($e, true)));
					}
				} catch (WindFtpException $e) {
					return new PwError('APPCENTER:upgrade.upload.fail', array($v));
				}
			} else {
				WindFolder::mkRecur(dirname(ROOT_PATH . $v));
				$r = @copy($source . DIRECTORY_SEPARATOR . $v, ROOT_PATH . $v);
				if (!$r) return new PwError('APPCENTER:upgrade.copy.fail', array($v));
			}
			$this->_log('copy file ' . $v);
		}
		if ($useFtp) $ftp->close();
		return true;
	}

	/**
	 * 备份
	 */
	public function backUp($fileList) {
		$root = Wind::getRealDir('ROOT:', true);
		$dest = Wind::getRealDir('DATA:backup', true) . DIRECTORY_SEPARATOR . $this->local;
		foreach ($fileList as $v => $hash) {
			if (is_file($root . DIRECTORY_SEPARATOR . $v)) {
				WindFolder::mkRecur(dirname($dest . DIRECTORY_SEPARATOR . $v));
				$r = @copy($root . DIRECTORY_SEPARATOR . $v, $dest . DIRECTORY_SEPARATOR . $v);
				
				if (!$r) return new PwError('APPCENTER:upgrade.backup.fail');
			}
		}
		$this->_log('back up file');
		return true;
	}

	public function flushLog() {
		if ($this->target) {
			$logFile = Wind::getRealDir('DATA:upgrade.log', true) . '/' . $this->target . '.log';
			if (file_exists($logFile) && $log = WindFile::read($logFile)) {
				Wekit::load('patch.PwUpgradeLog')->addLog($this->target, substr($log, 0, 60000));
			}
		}
	}

	private function _log($msg) {
		PwSystemHelper::log($msg, $this->target);
	}
}

?>
