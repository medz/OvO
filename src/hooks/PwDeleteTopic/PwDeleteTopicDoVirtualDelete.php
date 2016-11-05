<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:process.iPwGleanDoHookProcess');
Wind::import('SRV:recycle.dm.PwTopicRecycleDm');

/**
 * 帖子删除扩展服务接口--虚拟删除到回收站
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDeleteTopicDoVirtualDelete.php 15975 2012-08-16 09:40:09Z xiaoxia.xuxx $
 * @package forum
 */

class PwDeleteTopicDoVirtualDelete extends iPwGleanDoHookProcess {
	
	protected $record = array();

	public function gleanData($value) {
		$dm = new PwTopicRecycleDm();
		$dm->setTid($value['tid'])
			->setFid($value['fid'])
			->setOperateTime(Pw::getTime())
			->setOperateUsername($this->srv->user->username)
			->setReason($this->srv->reason);
		$this->record[] = $dm;
	}

	public function run($ids) {
		Wind::import('SRV:forum.dm.PwTopicDm');
		$dm = new PwTopicDm();
		$dm->setDisabled(2)->setTopped(0)->setDigest(0);
		Wekit::load('forum.PwThread')->batchUpdateThread($ids, $dm);
		
		Wind::import('SRV:forum.dm.PwReplyDm');
		$dm = new PwReplyDm();
		$dm->setDisabled(2);
		Wekit::load('forum.PwThread')->batchUpdatePostByTid($ids, $dm);

		Wekit::load('recycle.PwTopicRecycle')->batchAdd($this->record);
	}
}