<?php
Wind::import("WIND:ftp.AbstractWindFtp");
@set_time_limit(1000);
require_once Wind::getRealPath('LIB:utility.phpseclib.Net.SFTP');
/**
 * sftp 保存
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwSftpSave.php 24739 2013-02-19 11:20:21Z long.shi $
 * @package wind
 */
class PwSftpSave extends AbstractWindFtp {

	protected $port = 22;
	protected $rootPath = '.';
	
	/**
	 * @var Net_SFTP
	 */
	protected $conn;
	
	public function __construct($config = array()) {
		$this->initConfig($config);
		$this->conn = new Net_SFTP($this->server, $this->port, $this->timeout);
		if (!$this->conn->login($this->user, $this->pwd)) {
			throw new WindFtpException($this->user, WindFtpException::LOGIN_FAILED);
		}
		$this->initRootPath();
	}

	public function upload($localfile, $remotefile, $mode = null) {
		if (!in_array(($savedir = dirname($remotefile)), array('.', '/'))) {
			$this->mkdirs($savedir);
		}
		$remotefile = $this->rootPath . WindSecurity::escapePath($remotefile);
		return $this->conn->put($remotefile, $localfile, NET_SFTP_LOCAL_FILE);
	}
	
	/*
	 * (non-PHPdoc) @see AbstractWindFtp::rename()
	 */
	public function rename($oldName, $newName) {
		return $this->conn->rename($oldName, $newName);
	}
	
	/*
	 * (non-PHPdoc) @see AbstractWindFtp::delete()
	 */
	public function delete($filename) {
		return $this->conn->delete($filename);
	}
	
	public function getError() {
		return $this->conn->getLastSFTPError();
	}
	
	/*
	 * (non-PHPdoc) @see AbstractWindFtp::download()
	 */
	public function download($localfile, $remotefile = '', $mode = 'A') {
		return $this->conn->get($remotefile, $localfile);
	}
	
	/*
	 * (non-PHPdoc) @see AbstractWindFtp::fileList()
	 */
	public function fileList($dir = '.') {
		return $this->conn->nlist($dir);
	}
	
	/*
	 * (non-PHPdoc) @see AbstractWindFtp::close()
	 */
	public function close() {
		return $this->conn->disconnect();
	}
	
	/*
	 * (non-PHPdoc) @see AbstractWindFtp::mkdir()
	 */
	public function mkdir($dir) {
		return $this->conn->mkdir($dir);
	}
	
	/*
	 * (non-PHPdoc) @see AbstractWindFtp::changeDir()
	 */
	public function changeDir($dir) {
		return $this->conn->chdir($dir);
	}
	
	/*
	 * (non-PHPdoc) @see AbstractWindFtp::size()
	 */
	public function size($file) {
		return $this->conn->size($file);
	}
	
	/*
	 * (non-PHPdoc) @see AbstractWindFtp::pwd()
	 */
	protected function pwd() {
		return $this->conn->pwd();
	}
	
	/**
	 * 重设当前目录为初始化目录信息
	 */
	protected function initRootPath() {
		$r = $this->changeDir($this->dir ? $this->dir : '.');
		if (!$r) {
			throw new WindFtpException($this->dir, WindFtpException::COMMAND_FAILED_CWD);
		}
		$this->rootPath = $this->pwd();
	}
}

?>