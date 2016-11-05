<?php

/**
 * 默认站点首页
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: IndexController.php 24686 2013-02-05 04:39:28Z jieyin $
 * @package forum
 */

class IndexController extends PwBaseController {

	public function run() {
		header("Content-type:text/html;charset=" . Wekit::V('charset'));
		echo '这里是windid系统服务中心';exit;
	}
}