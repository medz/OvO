<?php
/**
 * 后台创始人服务类
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 2011-09-22 03:59:17Z yishuo $
 * @package wind
 */
class AdminFounderService {

	private $_founder = null;
	
	public function login($username, $password) {
		$founder = $this->getFounders();
		if (!$result = $this->checkPwd($founder[$username], $password)) {
			return new PwError('ADMIN:login.fail.user.illegal');
		}
		return array(AdminUserService::FOUNDER, $username, Pw::getPwdCode($result));
	}

	public function isLogin($username, $password) {
		if (!$this->isFounder($username)) {
			return array();
		}
		$founder = $this->getFounders();
		list($md5pwd) = explode('|', $founder[$username], 2);
		if (Pw::getPwdCode($md5pwd) != $password) {
			return array();
		}
		if (!$user = $this->loadUserService()->getUserByName($username)) {
			$user = array(
				'uid' => 0,
				'username' => $username, 
				'groupid' => 3
			);
		}
		return $user;
	}

	/**
	 * 添加创始人 
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 */
	public function add($username, $password, $email) {
		if (!$this->isWriteable()) return new PwError('ADMIN:founder.file.write.fail');
		$this->getFounders();
		if (isset($this->_founder[$username])) return new PwError(
			'ADMIN:founder.add.fail.username.duplicate');
		
		$user = $this->loadUserService()->getUserByName($username);
		if (!$password && !isset($user['password'])) return new PwError(
			'ADMIN:founder.add.fail.password.empty');
		$password || $password = $user['password'];
		
		$uid = isset($user['uid']) ? $user['uid'] : 0;
		$r = $this->loadUserService()->setUser($username, $password, $email, '3', $uid);
		if ($r instanceof PwError) return $r;
		
		$this->_founder[$username] = $this->encryptPwd($password);
		$r = $this->updateFounder();
		if ($r instanceof PwError) return $r;
		
		return true;
	}

	/**
	 * 编辑创始人
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @return boolean PwError
	 */
	public function edit($username, $password, $email) {
		if (!$this->isWriteable()) return new PwError('ADMIN:founder.file.write.fail');
		$this->getFounders();
		if (!isset($this->_founder[$username])) return new PwError('ADMIN:founder.edit.fail');
		
		$user = $this->loadUserService()->getUserByName($username);
		$uid = isset($user['uid']) ? $user['uid'] : 0;
		$r = $this->loadUserService()->setUser($username, $password, $email, '3', $uid);
		if ($r instanceof PwError) return $r;
		
		if ($password) {
			$this->_founder[$username] = $this->encryptPwd($password);
			$r = $this->updateFounder();
			if ($r instanceof PwError) return $r;
		}
		
		return true;
	}

	/**
	 * 校验密码
	 *
	 * @param string $pwd1 加密后
	 * @param string $pwd2 加密前
	 * @return false|pwd 不相等返回false，相同则返回md5pwd
	 */
	public function checkPwd($pwd1, $pwd2) {
		list($md5pwd, $salt) = explode('|', $pwd1, 2);
		if (md5($pwd2 . $salt) != $md5pwd) return false;
		return $md5pwd;
	}

	/**
	 * 创始密码人加密
	 *
	 * @param string $password
	 * @return string
	 */
	public function encryptPwd($password) {
		$salt = WindUtility::generateRandStr(6);
		return md5($password . $salt) . '|' . $salt;
	}

	/**
	 * 根据用户名删除创始人
	 *
	 * @param string $username
	 * @return boolean PwError
	 */
	public function del($username) {
		if (!$this->isWriteable()) return new PwError('ADMIN:founder.file.write.fail');
		$this->getFounders();
		if (!isset($this->_founder[$username])) return new PwError('ADMIN:founder.del.fail');
		unset($this->_founder[$username]);
		if (empty($this->_founder)) return new PwError('ADMIN:founder.del.fail.all');
		return $this->updateFounder();
	}

	/**
	 * 根据用户名查看是否创始人
	 *
	 * @param string $username
	 * @return boolean
	 */
	public function isFounder($username) {
		$founders = $this->getFounders();
		return isset($founders[$username]);
	}

	/**
	 * 读取创始人配置文件
	 *
	 * @return array
	 */
	public function getFounders() {
		if ($this->_founder === null) {
			$this->_founder = include($this->getFounderFilePath());
			is_array($this->_founder) || $this->_founder = array();
		}
		return $this->_founder;
	}

	/**
	 * 判断创始人配置文件是否可写
	 */
	public function isWriteable() {
		return is_writeable($this->getFounderFilePath());
	}

	/**
	 * 更新创始人信息
	 * 
	 * @return boolean PwError
	 */
	private function updateFounder() {
		$r = WindFile::savePhpData($this->getFounderFilePath(), $this->_founder);
		return $r ? $r : new PwError('ADMIN:founder.file.write.fail');
	}

	/**
	 * 获取创始人配置文件
	 *
	 * @return string
	 */
	private function getFounderFilePath() {
		return Wind::getRealPath(Wekit::app()->founderPath, true);
	}

	/**
	 * @return IAdminUserDependenceService
	 */
	private function loadUserService() {
		$userService = Wind::getComponent('adminUserService');
		if ($userService instanceof IAdminUserDependenceService) return $userService;
		throw new PwDependanceException('admin.userservice', 
			array('{service}' => __CLASS__, '{userservice}' => 'IAdminUserDependenceService'));
	}
}

?>