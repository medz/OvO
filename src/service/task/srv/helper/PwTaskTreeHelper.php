<?php
/**
 * 任务树帮助类
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskTreeHelper.php 4670 2012-02-23 01:41:42Z xiaoxia.xuxx $
 * @package service.task.srv.helper
 */
class PwTaskTreeHelper {

	/**
	 * 获得一颗有序的任务树
	 *
	 * @param array $tasks
	 * @return array
	 */
	public static function getTaskTree($tasks) {
		foreach ($tasks as $id => &$v) {
			if ($id === 'root') continue;
			if ($v['pre_task'] == 0) {
				$tasks['root']['items'][$id] = &$v;
			} else {
				if (!isset($tasks[$v['pre_task']])) continue;
				$tasks[$v['pre_task']]['items'][$id] = &$v;
			}
		}
		return $tasks;
	}
	
	/**
	 * 根据$id删除所有此id的后置任务
	 *
	 * @param array $taskTree 此$id的任务树
	 * @param int $id
	 */
	public static function clearAllNextNode($taskTree, &$toClear, $id) {
		if (isset($taskTree['items']) && !empty($taskTree['items'])) {
			foreach ($taskTree['items'] as $item) {
				unset($toClear[$item['id']]);
				self::clearAllNextNode($item, $toClear, $id);
			}
		}
	}
	
	/**
	 * 将树展示出来
	 *
	 * @param array $tree
	 * @param array $accepts 为空则不做过滤操作，否则要判断节点id是否在accept中
	 * @param string $split
	 * @return array 
	 */
	public static function cookTree($tree, $accepts = array(), $split = '|- ') {
		static $result = array();
		foreach ($tree as $k => $v) {
			if (!empty($accepts) && !in_array($k, $accepts)) continue;
			$result[$k] = $split . $v['title'];
			if (isset($v['items'])) {
				self::cookTree($v['items'], $accepts, '&nbsp;&nbsp;&nbsp;' . $split);
			}
		}
		return $result;
	}

}

?>