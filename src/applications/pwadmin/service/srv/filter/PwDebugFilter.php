<?php
/**
 * 应用开发模式
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwDebugFilter.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package wind
 */
class PwDebugFilter extends PwBaseFilter {
	/*
	 * (non-PHPdoc) @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		// TODO Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc) @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {
		Wekit::load('APPCENTER:service.srv.PwDebugApplication')->compile();
	}
}

?>