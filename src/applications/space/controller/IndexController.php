<?php
Wind::import('APPS:space.controller.SpaceBaseController');
/**
 * 新鲜事
 * 
 * @version $Id: IndexController.php 25054 2013-03-01 03:14:54Z jieyin $
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @package space
 */

class IndexController extends SpaceBaseController {
	
	/**
	 * (non-PHPdoc)
	 * @see wekit/wind/web/WindController::run()
	 */
	public function run() {
		$page = (int)$this->getInput('page','get');
		if ($page < 1) $page = 1;
		$this->setOutput('index', 'src');
		$this->setOutput($page, 'page');
		
		// seo设置

		Wind::import('SRV:seo.bo.PwSeoBo');
		$seoBo = PwSeoBo::getInstance();
		$lang = Wind::getComponent('i18n');
		$des = Pw::substrs($this->space->space['space_descrip'], 100, 0, false);
		if ($page == 1) {
			$seoBo->setCustomSeo($lang->getMessage('SEO:space.index.run.title', array($this->space->space['space_name'])), '', $des);
		} else {
			$seoBo->setCustomSeo($lang->getMessage('SEO:space.index.run.page.title', array($page, $this->space->space['space_name'])), '', $des);
		}
		Wekit::setV('seo', $seoBo);
	}
	
	/**
	 * 风格预览
	 * Enter description here ...
	 */
	public function demoAction() {
		if ($this->loginUser->uid < 1)  $this->showError('SPACE:user.not.login');
		$styleid = $this->getInput('id');
		$style = Wekit::load('APPCENTER:service.PwStyle')->getStyle($styleid);
		if (!$style) $this->showError('SPACE:fail');
		$this->space->space['space_style'] = $style['alias'];
		$this->setOutput(1, 'page');
		$this->setOutput($this->space, 'space');
		$this->setTemplate('index_run');
	}
	
	
	/**
	 * 回复
	 * 
	 */
	public function replyAction() {
		$id = (int)$this->getInput('id');
		Wind::import('LIB:ubb.PwSimpleUbbCode');
		Wind::import('LIB:ubb.config.PwUbbCodeConvertThread');
		Wind::import('SRV:attention.srv.PwFreshReplyList');
		$reply = new PwFreshReplyList($id);
		$fresh = $reply->getData();
		$replies = $reply->getReplies(7);
		$replies = Wekit::load('forum.srv.PwThreadService')->displayReplylist($replies);
		
		$count = count($replies);
		if ($count > 6) {
			$replies = array_slice($replies, 0, 6, true);
		}
		$this->setOutput($count, 'count');
		$this->setOutPut($replies, 'replies');
		$this->setOutPut($fresh, 'fresh');
	}
	
	/**
	 * 阅读更多
	 * 
	 */
	public function readAction() {
		Wind::import('SRV:forum.bo.PwThreadBo');
		$id = (int)$this->getInput('id');
		$fresh = Wekit::load('attention.PwFresh')->getFresh($id);
		if ($fresh['type'] == 1) {
			$thread = new PwThreadBo($fresh['src_id']);
			$array = $thread->info;
			$array['pid'] = 0;
		} else {
			$array = Wekit::load('forum.PwThread')->getPost($fresh['src_id']);
			$thread = new PwThreadBo($array['tid']);
		}
		Wind::import('LIB:ubb.PwUbbCode');
		Wind::import('LIB:ubb.config.PwUbbCodeConvertThread');
		$array['content'] = WindSecurity::escapeHTML($array['content']);
		$array['content'] = str_replace("\n", '<br />', $array['content']);
		$array['useubb'] && $array['content'] = PwUbbCode::convert($array['content'], new PwUbbCodeConvertThread($thread, $array, $this->loginUser));

		echo $array['content'];
		$this->setTemplate('');
	}
	
	public function freshAction() {
		list($id, $weiboid) = $this->getInput(array('id', 'weiboid'));
		$page = intval($this->getInput('page'));
		if ($weiboid) {
			Wind::import('SRV:attention.srv.dataSource.PwFetchFreshByTypeAndSrcId');
			$dataSource = new PwFetchFreshByTypeAndSrcId(3, array($weiboid));
		} else {
			Wind::import('SRV:attention.srv.dataSource.PwGetFreshById');
			$dataSource = new PwGetFreshById($id);
		}

		Wind::import('SRV:attention.srv.PwFreshDisplay');
		$freshDisplay = new PwFreshDisplay($dataSource);
		if (!$fresh = $freshDisplay->gather()) {
			$this->showError('fresh.exists.not');
		}
		Wind::import('LIB:ubb.PwSimpleUbbCode');
		Wind::import('LIB:ubb.config.PwUbbCodeConvertThread');
		Wind::import('SRV:attention.srv.PwFreshReplyList');

		$fresh = current($fresh);
		$id = $fresh['id'];

		$page < 1 && $page = 1;
		$perpage = 10;
		list($offset, $limit) = Pw::page2limit($page, $perpage);
		$reply = new PwFreshReplyList($id);
		$replies = $reply->getReplies($limit, $offset);
		$replies = Wekit::load('forum.srv.PwThreadService')->displayReplylist($replies);

		$this->setOutput($fresh, 'fresh');
		$this->setOutput($replies, 'replies');
		$this->setOutput($id, 'id');
		$this->setOutput($fresh['created_userid'], 'uid');

		$this->setOutput($page, 'page');
		$this->setOutput($perpage, 'perpage');
		$this->setOutput($fresh['replies'], 'count');
	}

}
