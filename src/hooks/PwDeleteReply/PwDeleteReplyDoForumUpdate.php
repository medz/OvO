<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:process.iPwGleanDoHookProcess');

/**
 * 帖子删除扩展服务接口--虚拟删除到回收站
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDeleteReplyDoForumUpdate.php 13278 2012-07-05 02:08:39Z jieyin $
 * @package forum
 */

class PwDeleteReplyDoForumUpdate extends iPwGleanDoHookProcess {
	
	public $record = array();
	
	public function gleanData($value) {
		if ($value['disabled'] == 0) {
			$this->record[$value['fid']]++;
		}
	}

	public function run($ids) {
		$srv = Wekit::load('forum.srv.PwForumService');
		foreach ($this->record as $fid => $value) {
			$srv->updateStatistics($fid, 0, -$value);
		}
	}
}