<?php
/**
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
 * @package service.seo.bo
 */
class PwSeoBo {
	/**
	 * 全局seo格式，即页面也没有设置默认的seo格式的情况
	 *
	 * @var array
	 */
	protected  $defaultSeo = array(
		'title' => '{sitename}', 
		'description' => '{sitename}', 
		'keywords' => '{sitename}');
	protected  $seo = array();
	protected  $codeData = array();
	protected  $default = array();
	private static $_instance = null;
	
	public function __construct() {
		$sitename = Wekit::C('site', 'info.name');
		$this->set('{sitename}', $sitename);
	}
	
	/**
	 * @return PwSeoBo
	 */
	public static function getInstance() {
		isset(self::$_instance) || self::$_instance = new self();
		return self::$_instance;
	}

	/**
	 * 初始化页面的seo格式
	 *
	 * 显示逻辑：
	 * 以帖子列表页为例：
	 * 如果版块设置了seo，则显示版块seo;
	 * 如果帖子列表页设置了，则显示帖子列表页的;
	 *
	 * @param string $mod        	
	 * @param string $page        	
	 * @param string $param        	
	 */
	public function init($mod, $page, $param = '0') {
		$this->default || $this->default = Wekit::load('APPS:seo.service.PwSeoExtends')->getDefaultSeoByPage(
			$page, $mod);
		/*
		 * 显示逻辑： 参数为0表示页面，参数不为0表示子页面。例如版块列表页参数为0，具体某个版块的页面参数为fid
		 * 1、参数为0，显示自定义的，否则显示默认值 2、参数不为0，显示自定义的，没有则显示参数为0的自定义的，也没有就显示参数为0的默认值
		 */
		if ($param != '0') {
			list($seo, $seo_0) = array(
				$this->_seoService()->getByModAndPageAndParamWithCache($mod, $page, $param), 
				$this->_seoService()->getByModAndPageAndParamWithCache($mod, $page, 0));
			$this->seo = self::_choose($seo, $seo_0, $this->default);
		} else {
			$result = $this->_seoService()->getByModAndPageAndParamWithCache($mod, $page, '0');
			$this->seo = $this->_choose($result, false, $this->default);
		}
	}

	/**
	 * 设置占位符的值
	 *
	 * @param string $code        	
	 * @param string $value        	
	 */
	public function set($code, $value = '') {
		if (is_array($code))
			$this->codeData = array_merge($this->codeData, $code);
		else
			$this->codeData[$code] = $value;
	}

	/**
	 * 返回seo值
	 *
	 * @return array
	 */
	public function getData() {
		empty($this->seo) && $this->seo = $this->defaultSeo;
		foreach ($this->seo as $k => &$v)
			$v = strip_tags(trim(strtr($v, $this->codeData), '-_ '));
		return $this->seo;
	}

	/**
	 * 此接口仅供无后台管理模式的seo值设置
	 *
	 * @param string $title        	
	 * @param string $keywords        	
	 * @param string $description        	
	 */
	public function setCustomSeo($title, $keywords, $description) {
		if ($title || $keywords || $description) {
			$this->seo = array(
				'title' => $title, 
				'keywords' => $keywords, 
				'description' => $description);
		}
	}

	public function setDefaultSeo($title, $keywords, $description) {
		$this->default = array(
			'title' => $title, 
			'keywords' => $keywords, 
			'description' => $description);
	}

	/**
	 *
	 * @return PwSeoService
	 */
	private function _seoService() {
		return Wekit::load('seo.srv.PwSeoService');
	}

	private function _choose($option1, $option2 = false, $default) {
		$tmp = array();
		if ($option2 !== false) {
			$tmp['title'] = $option1['title'] ? $option1['title'] : ($option2['title'] ? $option2['title'] : $default['title']);
			$tmp['description'] = $option1['description'] ? $option1['description'] : ($option2['description'] ? $option2['description'] : $default['description']);
			$tmp['keywords'] = $option1['keywords'] ? $option1['keywords'] : ($option2['keywords'] ? $option2['keywords'] : $default['keywords']);
		} else {
			$tmp['title'] = $option1['title'] ? $option1['title'] : $default['title'];
			$tmp['description'] = $option1['description'] ? $option1['description'] : $default['description'];
			$tmp['keywords'] = $option1['keywords'] ? $option1['keywords'] : $default['keywords'];
		}
		return $tmp;
	}
}

?>