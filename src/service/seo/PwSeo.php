<?php
/**
 * seo的数据服务层
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
 * 先确定论坛的三大页面，其他的页面由各个应用考虑。(此处具体见modules.seo.conf.seoExtends)
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
 * @package service.seo
 */
class PwSeo {
	
	/**
	 * 更新或添加seo数据(可批量操作)
	 *
	 * @param array $dms PwSeoDm数组
	 * @return boolean
	 */
	public function batchReplaceSeo($dms) {
		if (empty($dms)) return false;
		!is_array($dms) && $dms = array($dms);
		$data = array();
		foreach ($dms as $dm) {
			if (!$dm instanceof PwSeoDm) continue;
			if (($r = $dm->beforeUpdate()) instanceof PwError) return $r;
			$data[] = $dm->getData();
		}
		return $this->_seoDao()->batchReplaceSeo($data);
	}
	
	/**
	 * 获取seo数据, 
	 * 
	 * 以帖子列表页为例，
	 * param为0时表示查询列表页的seo，
	 * param为版块id时表示查询列表页下某个版块的seo，
	 *
	 * @param string $mod
	 * @param string $page
	 * @param string $param
	 * @return array
	 */
	public function getByModAndPageAndParam($mod, $page, $param) {
		return $this->_seoDao()->getByModAndPageAndParam($mod, $page, $param);
	}
	
	/**
	 * 根据模式和页面批量获取
	 *
	 * @param string $mod
	 * @param string $page
	 * @return array
	 */
	public function getByModAndPage($mod, $page) {
		return $this->_seoDao()->getByModAndPage($mod, $page);
	}
	
	/**
	 * 根据模式获取
	 *
	 * @param string $mod
	 * @return array
	 */
	public function getByMod($mod) {
		return $this->_seoDao()->getByMod($mod);
	}
	
	/**
	 * 根据参数获取多个seo数据
	 * 
	 * 参数组合：params = array(0,$fid)时表示查询列表页的普通seo和具体版块页的seo
	 *
	 * @param string $mod
	 * @param string $page
	 * @param array $params
	 * @return array
	 */
	public function getByParams($mod, $page, $params = array()) {
		if (empty($params)) return $this->_seoDao()->getByModAndPage($mod, $page);
		return $this->_seoDao()->getByParams($mod, $page, $params);
	}
	
	/**
	 * @return PwSeoDao
	 */
	private function _seoDao() {
		return Wekit::loadDao('seo.dao.PwSeoDao');
	}
}

?>