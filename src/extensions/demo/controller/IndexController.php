<?php
/**
 * 应用前台入口
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: IndexController.php 21266 2012-12-03 10:40:28Z long.shi $
 * @package demo
 */
class IndexController extends PwBaseController {
	
	public function run() {
		$app_name = 'demo';
		$this->setOutput($app_name, 'name');
	}
}

?>