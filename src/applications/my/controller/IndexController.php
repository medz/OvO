<?php

/**
 * 
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: IndexController.php 19275 2012-10-12 07:18:44Z xiaoxia.xuxx $
 * @package wind
 */
class IndexController extends PwBaseController {
	
	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$this->forwardRedirect(WindUrlHelper::createUrl('my/fresh/run'));
	}
}