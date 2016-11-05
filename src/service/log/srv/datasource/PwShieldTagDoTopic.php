<?php
defined('WEKIT_VERSION') || exit('Forbidden');
Wind::import('LIB:process.iPwGleanDoHookProcess');

/**
 * TODO 话题屏蔽
 *
 * @author xiaoxia.xu<xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwShieldTagDoTopic.php 22678 2012-12-26 09:22:23Z jieyin $
 * @package wind
 */
class PwShieldTagDoTopic extends iPwGleanDoHookProcess {
	
	public $tag = array();
	
	/* (non-PHPdoc)
	 * @see iPwGleanDoHookProcess::gleanData()
	 */
	public function gleanData($value) {
		$this->tag = $value;
	}

	/* (non-PHPdoc)
	 * @see iPwGleanDoHookProcess::run()
	 */
	public function run($id) {
		if ($this->tag) {
			$data = Wekit::load('forum.PwThread')->getThread($id);
			if (!$data) return false;
			$tag = $this->tag;
			
			/* @var $logSrv PwLogService */
			$logSrv = Wekit::load('log.srv.PwLogService');
			
			$langArgs = array();
			$langArgs['tag_url'] = WindUrlHelper::createUrl('tag/index/view', array('name' => $tag['tag_name']));
			$langArgs['tag'] = $tag['tag_name'];
			$langArgs['content_url'] = WindUrlHelper::createUrl('bbs/read/run', array('tid' => $data['tid']));
			$langArgs['content'] = $data['subject'];
			$langArgs['type'] = '帖子';
			
			$dm = new PwLogDm();
			$dm->setFid($data['fid'])
			->setTid($data['tid'])
			->setOperatedUser($data['created_userid'], $data['created_username']);
			//从话题中屏蔽帖子。管理日志添加
			$logSrv->addShieldTagLog($this->srv->user, $dm, $langArgs, $this->srv->ifShield);
		}
	}
}