<?php
Wind::import('APPS:seo.admin.AdminBaseSeoController');
/**
 * seo后台设置
 * 
 * 1、设置seo信息，可以直接输入文字，也支持参数选择；
 * 2、定位到输入框，可以弹出可以使用的参数，选择后显示到输入框；
 * 3、可以使用的参数：
 * 论坛首页：站点名称{sitename}
 * 帖子列表：站点名称{sitename}、版块名称{forumname}、版块简介{forumdescription}
 * 帖子阅读页：站点名称{sitename}、版块名称{forumname}、帖子标题{title}、帖子摘要{description}、帖子主题分类{classification}、标签{tags}
 * 
 * 显示逻辑：
 * 以帖子列表页为例：
 * 如果版块设置了seo，则显示版块seo;
 * 如果帖子列表页设置了，则显示帖子列表页的;
 * 最后如果都没有，显示全局seo
 * 
 * 默认数据：
 * 考虑当后台没有设置任何seo信息时的默认显示数据。
 * 先确定论坛的三大页面，其他的页面由各个应用考虑。(此处具体见service.seo.conf)
 * 论坛导航页：
 * title：论坛名称
 * keyword：空
 * description：空
 * 主题列表页：
 * title：版块名称_论坛名称
 * keyword：空
 * description：版块简介。如果没有设置，留空
 * 帖子阅读页：
 * title：帖子标题_版块名称_论坛名称
 * keyword：空
 * description：帖子摘要，截取内容前100字节
 * 
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package modules.seo.admin
 */
class ManageController extends AdminBaseSeoController {

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$url = $this->setTab('');
		$this->forwardAction($url);
	}

	/**
	 * bbs模式的seo设置
	 *
	 */
	public function bbsAction() {
		$this->setTab('bbs');
		
		$seo = $this->_seoDs()->getByMod('bbs');
		/* @var $forumService PwForumService */
		$forumService = Wekit::load('forum.srv.PwForumService');
		$map = $forumService->getForumMap();
		$forumList = array();
		foreach ($map[0] as $key => $value) {
			$forumList[$value['fid']] = $forumService->getForumsByLevel($value['fid'], $map);
		}
		$this->setOutput($forumList, 'forumList');
		$this->setOutput($map[0], 'cateList');
		$this->setOutput($seo, 'seo');
	}
	
	public function areaAction() {
		$this->setTab('area');
		$seo = $this->_seoDs()->getByMod('area');
		$this->setOutput($seo, 'seo');
		Wind::import('SRV:design.srv.vo.PwDesignPortalSo');
		$list = Wekit::load('design.PwDesignPortal')->searchPortal(new PwDesignPortalSo(), 0, 0);
		$this->setOutput($list, 'list');
		$this->setTemplate('areaseo_run');
	}
	
	public function likeAction() {
		$this->setTab('like');
		$seo = $this->_seoDs()->getByMod('like');
		$this->setOutput($seo, 'seo');
		$this->setTemplate('likeseo_run');
	}
	
	public function topicAction() {
		$this->setTab('topic');
		$seo = $this->_seoDs()->getByMod('topic');
		$this->setOutput($seo, 'seo');
		$this->setTemplate('topicseo_run');
	}

	/**
	 * 更新seo
	 *
	 */
	public function doRunAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		$seo = $this->getInput('seo', 'post');
		$mod = $this->getInput('mod');
		$data = array();
		foreach ($seo as $page => $list) {
			foreach ($list as $k => $v) {
				$dm = new PwSeoDm();
				$dm->setMod($mod)->setPage($page)->setParam($k)
				->setTitle($v['title'])->setKeywords($v['keywords'])->setDescription($v['description']);
				$data[] = $dm;
			}
		}
		$this->_seoService()->batchReplaceSeoWithCache($data);
		$this->showMessage('success');
	}
}

?>