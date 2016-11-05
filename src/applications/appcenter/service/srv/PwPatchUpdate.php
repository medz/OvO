<?php
Wind::import('APPCENTER:service.srv.helper.PwSystemHelper');
Wind::import('APPCENTER:service.srv.helper.PwApplicationHelper');
Wind::import('APPCENTER:service.srv.helper.PwFtpSave');
Wind::import('APPCENTER:service.srv.helper.PwSftpSave');
/**
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwPatchUpdate.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package wind
 */
class PwPatchUpdate {
	protected $bakFiles = array();
	protected $ftp;
	protected $tmpPath;
	
	public function __construct() {
		$this->tmpPath = DATA_PATH . 'tmp/' . Pw::getTime();
		$this->ftp = Wekit::cache()->get('system_patch_ftp');
	}
	
	/**
	 * 获取线上补丁列表
	 *
	 * @return Ambigous <multitype:, boolean, string>
	 */
	public function getOnlinePatchList() {
		$url = PwApplicationHelper::acloudUrl(
			array('a' => 'forward', 'do' => 'getSecurityPatch', 'pwversion' => NEXT_VERSION));
		$r = PwApplicationHelper::requestAcloudData($url);
		if (!is_array($r)) return '无法连接云平台!';
		if ($r['code'] !== '0') return $r['msg'];
		/* $r['info'] = array(
			array('id' => '9000001', 'name' => '更新', 'desc' => 'blabla', 'time' => '1323333', 'rule' => array(
				array('filename' => 'src/wekit.php', 'search' => base64_encode('Jianmin Chen'), 'replace' => base64_encode('Shi Long'), 'count' => '1', 'nums' => array('1'))))
		); */
		$temp = array();
		foreach ($r['info'] as $v) {
			$v['id'] = $v['name'];
			$temp[$v['id']] = WindConvert::convert($v, Wekit::V('charset'), 'utf8');
		}
		return $temp;
	}
	
	/**
	 * 校验补丁
	 *
	 * @return Ambigous <multitype:, multitype:multitype:string multitype:multitype:string multitype:string     >
	 */
	public function checkUpgrade() {
		$patches = $this->getOnlinePatchList();
		if (!is_array($patches)) return $patches;
		if ($patches) {
			$currentPatchId = 0;
			$patch = end($patches);
			$maxid = $patch['id'];
			if (defined('NEXT_FIXBUG')) {
				foreach ($patches as $p) {
					if ($p['id'] <= NEXT_FIXBUG) {
						$this->_ds()->addLog($p['id'], array(), 2);
					}
				}
			}
			$currentPatches = $this->_ds()->getByType(2);
			$result = array_diff_key($patches, $currentPatches);
		}
		return $result;
	}

	public function install($patch) {
		$tmpfiles = $this->bakFiles = array();
		WindFolder::mkRecur($this->tmpPath);
		if ($this->ftp && !is_object($this->ftp)) {
			try {
				$this->ftp = $this->ftp['sftp'] ? new PwSftpSave($this->ftp) : new PwFtpSave($this->ftp);
			} catch (WindFtpException $e) {
				return false;
			}
		}
		foreach ($patch['rule'] as $rule) {
			$rule['filename'] = $this->sortFile($rule['filename']);
			$filename = ROOT_PATH . $rule['filename'];
			$search = base64_decode($rule['search']);
			$replace = base64_decode($rule['replace']);
			$count = $rule['count'];
			$nums = $rule['nums'];
			
			$str = WindFile::read($filename);
			$realCount = substr_count($str, $search);
			if ($realCount != $count) {
				return new PwError('APPCENTER:upgrade.patch.update.fail', array($patch['id']));
			}
			$bakfile = basename($rule['filename']) . '.' . Pw::time2str(WEKIT_TIMESTAMP, 'Ymd') . '.bak';
			$bakfile = $this->ftp ? dirname($rule['filename']) . '/' . $bakfile : dirname($filename) . '/' . $bakfile;
			$tmpfile = tempnam($this->tmpPath, 'patch');
			
			$replacestr = PwSystemHelper::replaceStr($str, $search, $replace, $count, $nums);
			
			WindFile::write($tmpfile, $replacestr);
			if ($this->ftp) {
				try {
					$this->ftp->upload($filename, $bakfile);
					$this->ftp->upload($tmpfile, $rule['filename']);
				} catch (WindFtpException $e) {
					return false;
				}
			} else {
				if (!@copy($filename, $bakfile)) {
					return new PwError('APPCENTER:upgrade.copy.fail', array($rule['filename']));
				}
				if (!@copy($tmpfile, $filename)) {
					return new PwError('APPCENTER:upgrade.copy.fail', array($rule['filename']));
				}
			}
			$tmpfiles[] = $tmpfile;
			$this->bakFiles[] = $bakfile;
		}
		$this->_ds()->addLog($patch['id'], $patch, 2);
		return true;
	}
	
	public function clear() {
		if ($this->ftp) $this->ftp->close();
		WindFolder::clearRecur($this->tmpPath, true);
		Wekit::cache()->delete('system_patch_ftp');
	}

	public function revert() {
		if (!empty($this->bakFiles)) {
			foreach ($this->bakFiles as $backfile) {
				if ($this->ftp) {
					try {
						$this->ftp->upload($backfile, substr($backfile, -13));
					} catch (WindFtpException $e) {
						return false;
					}
				} else {
					if (!@copy($backfile, substr($backfile, -13))) {
						return false;
					}
				}
			}
		}
		return true;
	}
	
	public function writeAble($patch) {
		foreach ($patch['rule'] as $rule) {
			if (!PwSystemHelper::checkWriteAble(ROOT_PATH . $rule['filename'])) return false;
		}
		return true;
	}

	private function sortFile($file) {
		$directory = $this->_getDefaultDirectory();
		$sort = array(
			'HTML', 
			'ATTACH', 
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
			$search = PwSystemHelper::relative(WEKIT_PATH . $directory[$v]);
			$strtr[$search] = Wind::getRootPath($v);
		}
		$_file = ROOT_PATH . $file;
		foreach ($strtr as $search => $replace) {
			if (0 === strpos($_file, $search)) {
				$file = str_replace(ROOT_PATH, '', $replace . substr($_file, strlen($search)));
				break;
			}
		}
		return $file;
	}

	private function _getDefaultDirectory() {
		return array(
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
	}
	
	/**
	 * @return PwUpgradeLog
	 */
	private function _ds() {
		return Wekit::load('patch.PwUpgradeLog');
	}
}

?>