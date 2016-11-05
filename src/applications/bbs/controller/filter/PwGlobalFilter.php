<?php

/**
 * 系统默认全局filter
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-12-2
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwGlobalFilter.php 25328 2013-03-12 10:11:25Z jieyin $
 * @package src
 * @subpackage library.filter
 */
class PwGlobalFilter extends PwBaseFilter {
	
	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		/* 模板变量设置 */

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
		Wekit::setV('request', $request);

		$this->_setPreCache($request['m'], $request['mc'], $request['mca']);
		$loginUser = Wekit::getLoginUser();

		$config = Wekit::C('site');
		if ($config['visit.state'] > 0) {
			$service = Wekit::load('site.srv.PwSiteStatusService');
			$resource = $service->siteStatus($loginUser, $config);
			if ($resource instanceof PwError) {
				if (!($config['visit.state'] == 1 && $request['mc'] == 'u/login')) {
					$this->showError($resource->getError());
				}
			}
		}
		if (!in_array($request['mc'], array('u/login', 'u/register', 'u/findPwd')) && !$loginUser->getPermission('allow_visit')) {
			if ($loginUser->isExists()) {
				$this->showError(array('permission.visit.allow', array('{grouptitle}' => $loginUser->getGroupInfo('name'))));
			} else {
				$this->forwardRedirect(WindUrlHelper::createUrl('u/login/run'));
			}
		}
		if ($config['refreshtime'] > 0 && Wind::getApp()->getRequest()->isGet() && !Wind::getApp()->getRequest()->getIsAjaxRequest()) {
			if (Wekit::V('lastvist')->lastRequestUri == Wekit::V('lastvist')->requestUri && (Wekit::V('lastvist')->lastvisit + $config['refreshtime']) > Pw::getTime()) {
				$this->showError('SITE:refresh.fast');
			}
		}
		$this->_setPreHook($request['m'], $request['mc'], $request['mca']);

		$debug = $config['debug'] || !$config['css.compress'];
		Wekit::setGlobal(array('debug' => $debug ? '/dev' : '/build'), 'theme');
	}
	
	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {
		$this->runDesign();
		$this->updateOnline();
		$this->setOutput($this->runCron(), 'runCron');

		//门户管理模式 编译目录切换
		if ($this->getRequest()->getPost('design')) {
			$loginUser = Wekit::getLoginUser();
			$designPermission = $loginUser->getPermission('design_allow_manage.push');
			if ($designPermission > 0) {
				$dir = Wind::getRealDir('DATA:design.template');
				if (is_dir($dir)) WindFolder::rm($dir, true);
				$this->forward->getWindView()->compileDir = 'DATA:design.template';
			}
		}
		
		// SEO settings
		Wekit::setGlobal(NEXT_VERSION . ' ' . NEXT_RELEASE, 'version');
		$seo = Wekit::V('seo');
		Wekit::setGlobal($seo ? $seo->getData() : array('title' => Wekit::C('site', 'info.name')), 'seo');
		
		$this->setOutput($this->getRequest()->getIsAjaxRequest() ? '1' : '0', '_ajax_');
		
		/*[设置给PwGlobalFilters需要的变量]*/
		$_var = array(
			'current' => $this->forward->getWindView()->templateName,
			'a' => $this->router->getAction(),
			'c' => $this->router->getController(),
			'm' => $this->router->getModule());
		$this->getResponse()->setData($_var, '_aCloud_');
	}

	protected function _setPreCache($m, $mc, $mca) {
		$precache = Wekit::V('precache');
		if (isset($precache[$m])) Wekit::cache()->preset($precache[$m]);
		if (isset($precache[$mc])) Wekit::cache()->preset($precache[$mc]);
		if (isset($precache[$mca])) Wekit::cache()->preset($precache[$mca]);
	}

	protected function _setPreHook($m, $mc, $mca) {
		$prehook = Wekit::V('prehook');
		PwHook::preset($prehook['ALL']);
		PwHook::preset($prehook[Wekit::getLoginUser()->isExists() ? 'LOGIN' : 'UNLOGIN']);
		if (isset($prehook[$m])) PwHook::preset($prehook[$m]);
		if (isset($prehook[$mc])) PwHook::preset($prehook[$mc]);
		if (isset($prehook[$mca])) PwHook::preset($prehook[$mca]);
	}

	/**
	 * 门户流程控制
	 */
	protected function runDesign() {
		$request = Wekit::V('request');
		$pageName = $unique = '';
		$pk = 0;
		if ($request['mca'] == 'bbs/read/run') return true;//帖子阅读页在ReadController里处理
		$sysPage = Wekit::load('design.srv.router.PwDesignRouter')->get();
		if (!isset($sysPage[$request['mca']]))return false;
		list($pageName, $unique) = $sysPage[$request['mca']];
		$unique && $pk = $this->getInput($unique, 'get');
		if (!$pk) return false;
		Wind::import('SRV:design.bo.PwDesignPageBo');
    	$bo = new PwDesignPageBo();
    	$pageid = $bo->getPageId($request['mca'], $pageName, $pk);
		$pageid && $this->forward->getWindView()->compileDir = 'DATA:compile.design.'.$pageid;
		return true;
	}
	
	/**
	 * 首页绑定计划任务
	 *
	 * @return string Ambigous string>
	 */
	protected function runCron() {
		if (!$homeRouter = Wekit::C('site', 'homeRouter')) return '';
		$ishome = false;
		$request = Wekit::V('request');
		$httpRequest = $this->getRequest();
		if ($request['mca'] == $homeRouter['m'] . '/' . $homeRouter['c'] . '/' . $homeRouter['a']) {
			$ishome = true;
		}
		unset($homeRouter['m'], $homeRouter['c'], $homeRouter['a']);
		foreach ($homeRouter as $k => $v) {
			if (!$k) continue;
			if ($httpRequest->getAttribute($k) != $v) $ishome = false;
		}
		if (!$ishome) return '';
		$time = Pw::getTime();
		$cron = Wekit::load('cron.PwCron')->getFirstCron();
		if (!$cron || $cron['next_time'] > $time) return '';
		return WindUrlHelper::createUrl('cron/index/run/');
	}

	/**
	 * 在线服务  	
	 */
	protected function updateOnline() {
		$loginUser = Wekit::getLoginUser();
		$request = Wekit::V('request');
		if ($loginUser->uid > 0 && $request['mca'] == 'bbs/read/run') return false; //帖子阅读页在ReadController里处理
		if ($loginUser->uid > 0 && $request['m'] == 'space') return false; //空间在spaceBaseController里处理
		$online = Wekit::load('online.srv.PwOnlineService');
		// $service->clearNotOnline(); // 由计划任务清理
		if ($loginUser->uid > 0 && $request['mca'] == 'bbs/thread/run') {
			$createdTime = $online->forumOnline($this->getInput('fid'));
		} else {
			$clientIp = $loginUser->ip;
			$createdTime = $online->visitOnline($clientIp);
		}
		if (!$createdTime) return false;
		$dm = Wekit::load('online.dm.PwOnlineDm');
		$time = Pw::getTime();
		if ($loginUser->uid > 0) {
			$dm->setUid($loginUser->uid)->setUsername($loginUser->username)->setModifytime($time)->setCreatedtime($createdTime)->setGid($loginUser->gid)->setFid($this->getInput('fid', 'get'))->setRequest($request['mca']);
			Wekit::load('online.PwUserOnline')->replaceInfo($dm);
		} else {
			$dm->setIp($clientIp)->setCreatedtime($createdTime)->setModifytime($time)->setFid($this->getInput('fid', 'get'))->setTid($this->getInput('tid', 'get'))->setRequest($request['mca']);
			Wekit::load('online.PwGuestOnline')->replaceInfo($dm);
		}
	}
}
?>