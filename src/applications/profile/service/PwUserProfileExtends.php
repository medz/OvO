<?php

/**
 * 菜单扩展服务
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserProfileExtends.php 23702 2013-01-15 06:09:39Z xiaoxia.xuxx $
 * @package src.products.u.service
 */
class PwUserProfileExtends extends PwBaseHookService {

	public $current = '';
	public $left = '';
	/**
	 * 当前登录用户
	 *
	 * @var PwUserBo
	 */
	public $user = null;
	
	public function __construct(PwUserBo $userBo) {
		parent::__construct();
		$this->user = $userBo;
	}
	
	/**
	 * 设置当前的菜单项
	 *
	 * @param string $left
	 * @param string $tab
	 */
	public function setCurrent($left, $tab) {
		$this->current = $left . '_' . $tab;
		$this->left = $left;
	}
	
	/**
	 * 执行
	 */
	public function execute() {
		if (($r = $this->runWithVerified('check')) instanceof PwError) {
			return $r;
		}
		return $this->runDo('execute');
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseHookService::_getInterfaceName()
	 */
	protected function _getInterfaceName() {
		return 'PwProfileExtendsDoBase';
	}
}