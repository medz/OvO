<?php
defined('WEKIT_VERSION') || exit('Forbidden');
Wind::import('LIB:process.iPwGleanDoHookProcess');
/**
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com> Dec 3, 2012
 * @link http://www.phpwind.com
 * @copyright Copyright © 2003-2010 phpwind.com
 * @license
 */

class PwShieldTagDoReply extends iPwGleanDoHookProcess {

	public $tag = false;
	
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
			$data = Wekit::load('forum.PwThread')->getPost($id);
			if (!$data) return false;
			$tag = $this->tag;
			
			/* @var $logSrv PwLogService */
			$logSrv = Wekit::load('log.srv.PwLogService');
			
			$langArgs = array();
			$langArgs['tag_url'] = WindUrlHelper::createUrl('tag/index/view', array('name' => $tag['tag_name']));
			$langArgs['tag'] = $tag['tag_name'];
			$langArgs['content_url'] = WindUrlHelper::createUrl('bbs/read/run', array('tid' => $data['tid']), $data['pid']);
			$langArgs['content'] = $data['subject'];
			$langArgs['type'] = '帖子回复';
			
			$dm = new PwLogDm();
			$dm->setFid($data['fid'])
			->setTid($data['tid'])
			->setPid($data['pid'])
			->setOperatedUser($data['created_userid'], $data['created_username']);
			//从话题中屏蔽帖子。管理日志添加
			$logSrv->addShieldTagLog($this->srv->user, $dm, $langArgs, $this->srv->ifShield);
		}
	}
}