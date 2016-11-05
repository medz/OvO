<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:process.iPwGleanDoHookProcess');

/**
 * 帖子删除扩展服务接口--删除帖子新鲜事
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDeleteReplyDoFreshDelete.php 8959 2012-04-28 09:06:05Z jieyin $
 * @package forum
 */

class PwDeleteReplyDoFreshDelete extends iPwGleanDoHookProcess {
	
	/*
	protected $recode = array();

	public function gleanData($value) {
		if ($value['fid'] == 0) {
			$this->recode[] = $value['tid'];
		}
	}*/

	public function run($ids) {
		Wekit::load('attention.PwFresh')->batchDeleteByType(PwFresh::TYPE_THREAD_REPLY, $ids);
	}
}