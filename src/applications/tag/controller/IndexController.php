<?php
Wind::import('LIB:base.PwBaseController');
/**
 * 话题前台
 *
 */
class IndexController extends PwBaseController {
	private $hotTag = 4;
	private $hotContents = 3;
	private $perpage = 10;
	private $defaultType = 'threads';
	private $attentionTagList = 10;
	private $hotTagList = 10;	//热门话题显示数
	
	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$typeName = $this->defaultType;
		$categoryId = intval($this->getInput('categoryid','get'));
		$alias = $this->getInput('alias','get');
		$tagServicer = $this->_getTagService();
		$hotTags = $tagServicer->getHotTags($categoryId,20);
		$tagIds = array();
		foreach ($hotTags as $k => $v) {
			$attentions = $this->_getTagAttentionDs()->getAttentionUids($k,0,5);
			$hotTags[$k]['weight'] = 0.7 * $v['content_count'] + 0.3 * $v['attention_count'];
			$hotTags[$k]['attentions'] = array_keys($attentions);
			$tagIds[] = $k;
		}
		usort($hotTags, array($this, 'cmp'));
		
		$myTags = $this->_getTagAttentionDs()->getAttentionByUidAndTagsIds($this->loginUser->uid,$tagIds);

		$this->setOutput($myTags, 'myTags');
		$this->setOutput($hotTags, 'hotTags');
		$this->setOutput($categoryId, 'categoryId');
		
		//seo设置
		Wind::import('SRV:seo.bo.PwSeoBo');
		$seoBo = PwSeoBo::getInstance();
		$seoBo->init('topic', 'hot');
		Wekit::setV('seo', $seoBo);
	}
	
	/**
	 * 我的话题
	 */
	public function myAction(){
		if ($this->loginUser->uid < 1) {
			$this->forwardRedirect(WindUrlHelper::createUrl('u/login/run', array('backurl' => WindUrlHelper::createUrl('tag/index/my'))));
		}
		$typeName = $this->defaultType;
/*		list($page) = $this->getInput(array('page'));
		$page = $page ? $page : 1;
		list($start, $limit) = Pw::page2limit($page, $this->attentionTagList);*/
		$tagServicer = $this->_getTagService();
		//获取我关注的话题列表
		$myTagsCount = $this->_getTagAttentionDs()->countAttentionByUid($this->loginUser->uid);
		if ($myTagsCount) {
			$relations = $this->_getTagDs()->getAttentionByUid($this->loginUser->uid,0,50);
			$relationTagIds = array_keys($relations);
			$myTagList = array_slice($relationTagIds,0,10);
			$myTagsList = $this->_getTagDs()->fetchTag($relationTagIds);
			$tmpArray = array();
			foreach ($myTagList as $v) {
				$tmpArray[$v] = $myTagsList[$v];
			}
			$myTags['tags'] = $tmpArray;
			$myTags['step'] = $myTagsCount > $this->attentionTagList ? 2 : '';
			$ifcheck = !$this->_checkAllowManage() ? 1 : '';
			$tagContents = $params = $relatedTags = array();
			$tmpTagContent = $myTags['tags'] ? array_slice($myTags['tags'], 0, 5, true) : array();
			foreach($tmpTagContent as $k=>$v) {
				$contents = $tagServicer->getContentsByTypeName($k,$typeName,$ifcheck,0,$this->hotContents);
				if ($contents) {
					$tagContents[$k] = $contents;
					foreach ($contents as $k2=>$v2) {
						$params[] = $k2;
					}
				}
			}
			$moreTags = array_diff_key($myTagsList, $tagContents);
			$params and $relatedTags = $tagServicer->getRelatedTags($typeName,$params);
		}
		//热门话题
		$this->_setHotTagList($tagServicer->getHotTags(0,20));
		$this->setOutput($tagContents, 'tagContents');
		$this->setOutput($relatedTags, 'relatedTags');
		$this->setOutput($myTags, 'myTags');
		$this->setOutput($moreTags, 'moreTags');
		$this->setOutput($myTagsList, 'myTagsList');
		$this->setOutput($myTagsCount, 'myTagsCount');
		//$this->setOutput($page, 'page');
		//$this->setOutput($this->perpage, 'perpage');
		
		// seo设置
		Wind::import('SRV:seo.bo.PwSeoBo');
		$seoBo = PwSeoBo::getInstance();
		$lang = Wind::getComponent('i18n');
		$seoBo->setCustomSeo($lang->getMessage('SEO:tag.index.my.title'), '', '');
		Wekit::setV('seo', $seoBo);
	}
	
	/**
	 *
	 * 关注话题
	 */
	public function attentionAction(){
		if ($this->loginUser->uid < 1) {
			$this->showError('USER:user.not.login');
		}
		$tagId = intval($this->getInput('id'));
		$type = $this->getInput('type');
		$uid = $this->loginUser->uid;
		if ($type == 'add') {
			$result = $this->_getTagService()->addAttention($uid,$tagId);

			if ($result instanceof PwError) $this->showError($result->getError());
			$this->showMessage('TAG:add.success');
		} else {
			$this->_getTagService()->deleteAttention($uid, $tagId);
			$this->showMessage('TAG:del.success');
		}
		
	}
	
	/**
	 * 话题聚合页 - 内容
	 *
	 * @return void
	 */
	public function viewAction(){
		list($id,$page,$perpage,$type,$tagName) = $this->getInput(array('id', 'page', 'perpage', 'type', 'name'));
		$page = $page ? $page : 1;
		if (!$id && $tagName) {
			$tagName = rawurldecode($tagName);
			$tag = $this->_getTagDs()->getTagByName($tagName);
			$id = $tag['tag_id'];
		} else {
			$tag = $this->_getTagDs()->getTag($id);
		}
		if (!$tag) $this->showError("TAG:id.empty", "tag/index/run");
		if ($tag['parent_tag_id']) {
			$tag = $this->_getTagDs()->getTag($tag['parent_tag_id']);
			$id = $tag['tag_id'];
		}
		// 是否关注
		$tag['attention'] = $this->_getTagAttentionDs()->isAttentioned($this->loginUser->uid,$id);

		//获取我关注的话题列表
		list($myTagsCount,$myTags['tags']) = $this->_getTagService()->getAttentionTags($this->loginUser->uid,0,$this->attentionTagList);
		$myTags['step'] = $myTagsCount > $this->attentionTagList ? 2 : '';
		//热门话题
		$this->_setHotTagList($this->_getTagService()->getHotTags(0,20));
		if ($type == 'users') {
			$perpage = 50;
			list($start, $limit) = Pw::page2limit($page, $perpage);
			list($count, $users) = $this->_getTagService()->getTagMembers($id,$start,$limit);
			$this->setOutput($users, 'users');
		} else {
			$perpage = $perpage ? $perpage : $this->perpage;
			list($start, $limit) = Pw::page2limit($page, $perpage);
			// to du $ifcheck
			$ifcheck = !$this->_checkAllowManage() ? 1 : '';
			$typeId = $this->_getTagService()->getTypeIdByTypeName($this->defaultType);
			$count = $this->_getTagDs()->countRelationsByTagId($id,$typeId,$ifcheck);
		//	$tag['content_count'] = $count;
			if ($count > 0) {
				$tmpTags = $this->_getTagService()->getContentsByTypeName($id,$this->defaultType,$ifcheck,$start,$limit);
				foreach ($tmpTags as $k=>$v) {
					$params[] = $k;
					$v['type_id'] = $typeId;
					$v['param_id'] = $v['tid'];
					$contents[$k] = $v;
				}
				$params and $relatedTags = $this->_getTagService()->getRelatedTags($this->defaultType,$params);
				$this->setOutput($relatedTags, 'relatedTags');
				$this->setOutput($contents, 'contents');
			}
		}
		$args = array(
			'id' 	=> $tag['tag_id'],
			'type' 	=> $type,
		);
		$this->setOutput($this->_checkAllowManage(), 'allowManage');
		$this->setOutput($myTags, 'myTags');
		$this->setOutput($count, 'count');
		$this->setOutput($page, 'page');
		$this->setOutput($perpage, 'perpage');
		$this->setOutput($tag, 'tag');
		$this->setOutput($args, 'args');
		
		// seo设置
		Wind::import('SRV:seo.bo.PwSeoBo');
		$seoBo = PwSeoBo::getInstance();
		if ($type == 'users') {
			$lang = Wind::getComponent('i18n');
			$seoBo->setCustomSeo($lang->getMessage('SEO:tag.index.view.users.title', array($tag['tag_name'])), '', '');
		} else {
			if ($tag['seo_title'] || $tag['seo_keywords'] || $tag['seo_description']) {
				$seoBo->setCustomSeo($tag['seo_title'],$tag['seo_keywords'],$tag['seo_description']);
			} else {
				$lang = Wind::getComponent('i18n');
				$seoBo->setCustomSeo($lang->getMessage('SEO:tag.index.view.title', array($tag['tag_name'])), '', '');
			}
		}
		Wekit::setV('seo', $seoBo);
	}
	
	/**
	 * 屏蔽操作
	 *
	 * @return void
	 */
	public function doshieldAction(){
		// 是否登录
		if ($this->loginUser->uid < 1) {
			$this->showError('USER:user.not.login');
		}
		// 是否有权限
		if ($this->_checkAllowManage() !== true) {
			$this->showError('TAG:right.tag_allow_manage.error');
		}
		list($id,$typeId,$paramId,$ifcheck) = $this->getInput(array('id','type_id','param_id','ifcheck'));
		$increseCount = $ifcheck ? 1 : -1;
		Wind::import('SRV:tag.dm.PwTagDm');
		$dm = new PwTagDm($id);
		$dm->setIfCheck($ifcheck)
			->addContentCount($increseCount);
		$result = $this->_getTagDs()->updateRelation($typeId,$paramId,$id,$dm);
		$this->_getTagDs()->updateTag($dm);
		Wind::import('SRV:log.srv.operator.PwAddTagShieldLog');
		$log = new PwAddTagShieldLog($id, $typeId, $paramId, $this->loginUser);
		$log->setIfShield($ifcheck)
			->execute();
		
		!$result && $this->showError('fail');
		$this->showMessage('success');
	}
	
	/**
	 * 关注话题榜单
	 *
	 * @return void
	 */
	public function attentionlistAction(){
		$step = (int)$this->getInput('step');
		$step < 1 && $this->showError('data.error');
		list($start, $limit) = Pw::page2limit($step, $this->attentionTagList);
		list($myTagsCount,$myTags['tags']) = $this->_getTagService()->getAttentionTags($this->loginUser->uid,$start,$limit);
		$countStep = ceil($myTagsCount/$this->attentionTagList);
		$step < $countStep && $myTags['step'] = $step+1;
		Pw::echoJson($myTags);exit;
	}
	
	/**
	 * 编辑帖子阅读页话题
	 *
	 * @return void
	 */
	public function editReadTagAction(){
		// 是否登录
		if ($this->loginUser->uid < 1) {
			$this->showError('USER:user.not.login');
		}
		list($tid,$tagnames) = $this->getInput(array('tid','tagnames'));
		$tagnames = $tagnames ? $tagnames : array();
		// 是否有权限
		if ($this->_checkAllowEdit($tid) !== true) {
			$this->showError('TAG:right.tag_allow_edit.error');
		}
		$count = count($tagnames);
		$count > 5 && $this->showError("Tag:tagnum.exceed");
		Wind::import('SRV:tag.dm.PwTagDm');
		if ($count == 1) {
			$dm = new PwTagDm();
			$dm->setName($tagnames['0']);
			if(($result = $dm->beforeUpdate()) instanceof PwError) {
				$this->showError($result->getError());
			}
		}
		// 敏感词
		$content = implode(' ', $tagnames);
		$wordFilter = Wekit::load('SRV:word.srv.PwWordFilter');
		list($type, $words) = $wordFilter->filterWord($content);
		if ($type) {
			$this->showError("WORD:content.error");
		}
		$typeId = $this->_getTagService()->getTypeIdByTypeName($this->defaultType);
		$dmArray = array();
		foreach ((array)$tagnames as $value) {
			$value = trim($value);
			if(Pw::strlen($value) > 15) {
				continue;
			}
			$dm = new PwTagDm();
			if (($result = $dm->checkTagName($value)) instanceof PwError) {
				$this->showError($result->getError());
			}
			$dmArray[$value] =
				$dm->setName($value)
					->setTypeId($typeId)
					->setParamId($tid)
					->setIfHot(1)
					->setCreatedTime(Pw::getTime())
					->setCreateUid($this->loginUser->uid)
			;
		}
		$result = $this->_getTagService()->updateTags($typeId,$tid,$dmArray);
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage('success');
	}
	
	/**
	 * 获取热门话题
	 *
	 * @return void
	 */
	public function getHotTagsAction(){
		$hotTags = $this->_getTagService()->getHotTags('',$this->hotTagList);
		Pw::echoJson($hotTags);exit;
	}
	
	/**
	 * 话题小名片
	 *
	 * @return void
	 */
	public function cardAction(){
		$name = $this->getInput('name');
		$tag = $this->_getTagService()->getTagCard($name,$this->loginUser->uid);
		$this->setOutput($tag, 'tag');
	}
	
	protected function _formatTags($tags) {
		if (!$tags) return false;
		$tagname = array();
		foreach ($tags as $v) {
			$tagname[] = $v['tag_name'];
		}
		return implode(',',$tagname);
	}
	
	/**
	 * 检测屏蔽权限
	 *
	 * @return void
	 */
	private function _checkAllowManage() {
		if ($this->loginUser->getPermission('tag_allow_manage') < 1) {
			return false;
		}
		return true;
	}
	
	/**
	 * 检测编辑权限
	 *
	 * @return void
	 */
	private function _checkAllowEdit($tid) {
		$thread = Wekit::load('forum.PwThread')->getThread($tid);
		if (!($thread['created_userid'] == $this->loginUser->uid && $this->loginUser->getPermission('tag_allow_add')) && $this->loginUser->getPermission('tag_allow_edit') < 1) {
			return false;
		}
		return true;
	}
	
	/**
	 * 设置热门话题
	 *
	 * @return void
	 */
	private function _setHotTagList($hotTags){
		$hotTags = array_slice($hotTags,0, $this->hotTagList);
		$this->setOutput($hotTags,'hotTagList');
	}
	
	private function cmp($a, $b) {
		    return strcmp($b["weight"], $a["weight"]);
	}
	
	/**
	 * @return PwTag
	 */
	private function _getTagDs() {
		return Wekit::load('tag.PwTag');
	}
	
	/**
	 * @return PwTagService
	 */
	private function _getTagService() {
		return Wekit::load('tag.srv.PwTagService');
	}
	
	/**
	 * 分类DS
	 *
	 * @return PwTagCateGory
	 */
	private function _getTagCateGoryDs(){
		return Wekit::load('tag.PwTagCateGory');
	}
	
	/**
	 * 关注DS
	 *
	 * @return PwTagAttention
	 */
	private function _getTagAttentionDs(){
		return Wekit::load('tag.PwTagAttention');
	}
}
?>