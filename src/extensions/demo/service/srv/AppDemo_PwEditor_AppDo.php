<?php
defined('WEKIT_VERSION') or exit(403);
/**
 * 编辑器扩展
 *
 * @author pw <pw@aliyun-inc.com>
 * @copyright http://www.phpwind.net/u-htm-uid-1793211.html
 * @license http://www.phpwind.net/u-htm-uid-1793211.html
 */
class AppDemo_PwEditor_AppDo {
	
	/**
	 * @param array $var
	 * @return array
	 */
	public function appDemoDo($var) {
		$var[] = array(
			'name' => 'demo',
			'params' => array('len' => 8, 'age' => 2)
		);
		return $var;
	}
}

?>