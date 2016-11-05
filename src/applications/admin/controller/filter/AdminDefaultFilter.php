<?php

/**
 * 后台管理平台默认过滤器
 * 后台管理平台默认过滤器，职责:<ol>
 * <li>设置后台所需全局变量信息</li>
 * <li>配置信息设置</li>
 * <li>检查后台用户是否登录</li>
 * </ol>
 * 
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-13
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: AdminDefaultFilter.php 24363 2013-01-29 08:24:25Z jieyin $
 * @package wind
 */
class AdminDefaultFilter extends PwBaseFilter {
	
	/*
	 * (non-PHPdoc) @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {

		$url = array();
		$var = Wekit::url();
		$url['base'] = $var->base;
		$url['res'] = $var->res;
		$url['css'] = $var->css;
		$url['images'] = $var->images;
		$url['js'] = $var->js;
		$url['attach'] = $var->attach;
		$url['themes'] = $var->themes;
		$url['extres'] = $var->extres;
		Wekit::setGlobal($url, 'url');

		$request = array(
			'm' => $this->router->getModule(),
			'c' => $this->router->getController(),
			'a' => $this->router->getAction(),
		);
		$request['mc'] = $request['m'] . '/' . $request['c'];
		$request['mca'] = $request['mc'] . '/' . $request['a'];
		Wekit::setGlobal($request, 'request');

		if (in_array($request['mca'], array('default/index/login', 'default/index/showVerify', 'appcenter/app/upload'))) {
			return;
		}
		
		/* @var $userService AdminUserService */
		$userService = Wekit::load('ADMIN:service.srv.AdminUserService');
		/* @var $safeService AdminSafeService */
		$safeService = Wekit::load('ADMIN:service.srv.AdminSafeService');
		/* @var $founderService AdminFounderService */
		$founderService = Wekit::load('ADMIN:service.srv.AdminFounderService');
		
		/* @var $loginUser AdminUserBo */
		$loginUser = Wekit::getLoginUser();
		if (!$loginUser->isExists() || (!$founderService->isFounder($loginUser->username) && !$safeService->ipLegal(
			Wind::getComponent('request')->getClientIp()))) {
			if (!$this->getRequest()->getIsAjaxRequest()) {
				$this->forward->forwardAction('default/index/login');
			} else {
				$this->errorMessage->addError('logout', 'state');
				$this->errorMessage->sendError('ADMIN:login.fail.not.login');
			}
		}
		
		$_unVerifyTable = array('home', 'index', 'find');
		if (!in_array(strtolower($request['c']), $_unVerifyTable)) {
			if ($request['c'] != 'adminlog') {
				$logService = Wekit::load('ADMIN:service.srv.AdminLogService');
				$logService->log($this->getRequest(), $loginUser->username, $request['m'], $request['c'], $request['a']);
			}
			$_result = $userService->verifyUserMenuAuth($loginUser, $request['m'], $request['c'], $request['a']);
			if ($_result instanceof PwError) $this->errorMessage->sendError($_result->getError());
		}
	}
	
	/*
	 * (non-PHPdoc) @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {
	}
}

?>