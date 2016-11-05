<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignModuleBo.php 22756 2012-12-27 03:27:36Z gao.wanggao $ 
 * @package 
 */
class PwDesignModuleBo {
	public static $stdId;
	public $moduleid;
	private $_module;
	
	public function __construct($moduleid) {
		$this->moduleid = $moduleid;
		$this->_setModule();
	}
	
	/**
	 * 为模版缓存id
	 * 
	 */
	public function setStdId() {
		self::$stdId = $this->moduleid;
	}
	
	/**
	 * model类型临时更改
	 * 
	 * @param string $model
	 */
	public function setModel($model) {
		$this->_module['model_flag'] = $model;
	}
	
	public function getModel(){
		return isset($this->_module['model_flag']) ? $this->_module['model_flag'] : null;
	}
	
	public function getModule(){
		return $this->_module;
	}
	
	public function getProperty() {
		return empty($this->_module['module_property']) ? array() : unserialize($this->_module['module_property']);
	}
	
	public function getVoParam() {
		$property = $this->getProperty();
		unset($property['titlenum'],$property['desnum'],$property['timefmt'],$property['isblank'],$property['compid'],$property['limit']);
		return $property;
	}
	public function getView() {
		$view = array();
		$property = $this->getProperty();
		$view['titlenum'] = (int)$property['titlenum'];
		$view['desnum'] = (int)$property['desnum'];
		$view['timefmt'] = $property['timefmt'];
		$view['isblank'] = $property['isblank'];
		$view['compid'] = (int)$property['compid'];
		$view['limit'] = (int)$property['limit'];
		return $view;
	}
	
	public function getCache() {
		return  unserialize($this->_module['module_cache']);
	}
	
	public function getLimit() {
		$set = $this->getView();
		return (isset($set['limit']) && $set['limit']> 0) ? $set['limit'] : 10;
	}
	
	public function getTitlenum() {
		$set = $this->getView();
		return isset($set['titlenum']) ? $set['titlenum'] : null;
		
	}
	
	public function getDesnum() {
		$set = $this->getView();
		return  isset($set['desnum']) ? $set['desnum'] : null;
	}
	
	public function getStyle() {
		return empty($this->_module['module_style']) ? array() : unserialize($this->_module['module_style']);
	}
	
	public function getTitle() {
		return empty($this->_module['module_title']) ? array() : unserialize($this->_module['module_title']);
	}
	
	public function getTemplate() {
		return $this->_module['module_tpl'];
	}
	
	/**
	 * 获取当前模块自定义标签
	 * Enter description here ...
	 */
	public function getSignKey() {
		Wind::import('SRV:design.bo.PwDesignModelBo');
		$bo = new PwDesignModelBo($this->getModel());
		return $bo->getSignKeys();
	}
	
	/**
	 * 标准化标签
	 * Enter description here ...
	 */
	public function getStandardSign() {
		Wind::import('SRV:design.bo.PwDesignModelBo');
		$bo = new PwDesignModelBo($this->getModel());
		return $bo->getStandardSign();
	}
	
	/**
	 * 获取展示的数据
	 * Enter description here ...
	 */
	public function getData($isextend = false, $isreserv = true) {
		$srv = Wekit::load('design.srv.display.PwDesignDisplay');
		return $srv->getModuleData($this->moduleid, $isextend, $isreserv);
	}
	
	/**
	 * 获取推送的数据
	 * Enter description here ...
	 */
	public function getPushData($limit = 10, $start = 0, $status = null) {
		$data = Wekit::load('design.PwDesignPush')->getPushList($this->moduleid, $limit, $start, $status);
		foreach ($data AS $k=>$v) {
			$_tmp = unserialize($v['push_extend']);
			$standard = unserialize($v['push_standard']);
			$data[$k]['title'] = $_tmp[$standard['sTitle']];
			$data[$k]['url'] = $_tmp[$standard['sUrl']];
			$data[$k]['intro'] = $_tmp[$standard['intro']];
    	}
    	return $data;
	}
	
	/**
	 * 允许数据更新的时间
	 * 
	 * @return 允许更新的开始时间，允许更新的结束时间，更新的时间
	 * 
	 */
	public function refreshTime($time) {
		$expired = $this->getCache();
		if ($expired['expired'] < 1) return array($time, 0, 0);
		list($y, $m, $d) = explode('-',Pw::time2str($time,'Y-m-d'));
		$start =  Pw::str2time($y.'-'.$m.'-'.$d.' '.$expired['start_hour'].':'.$expired['start_minute'].':0');
		if ($expired['end_hour'] < $expired['start_hour']) $d++;
		$end =  Pw::str2time($y.'-'.$m.'-'.$d.' '.$expired['end_hour'].':'.$expired['end_minute'].':0');
		if ($start == $end) $end += 86400;
		if ($time < $start) {
			$refreshTime = $start;
		} elseif($time > $start && $time < $end ) {
			$refreshTime = $time + (int)$expired['expired'] * 60;
		} else {
			$refreshTime = $start + 86400;
		} 
		return array($start, $end, $refreshTime);
	}
	
	public function getTitleHtml() {
		$html = '';
		$titles = $this->getTitle();
		$styleSrv = Wekit::load('design.srv.PwDesignStyle');
		foreach ((array)$titles['titles'] AS $k=>$v) {
			$_tmp = array(
				'title'=>WindSecurity::escapeHTML($v['title']),
				'link'=>$v['link'],
				'image'=>$v['image'],
				'float'=>$v['float'],
				'margin'=>$v['margin'],
				'fontsize'=>$v['fontsize'],
				'fontcolor'=>$v['fontcolor'],
				'fontbold'=>$v['fontbold'],
				'fontunderline'=>$v['fontunderline'],
				'fontitalic'=>$v['fontitalic'],
			);
			$style = $styleSrv->buildTitleStyle($_tmp);
			$styleSrv->setStyle($style);
			list($dom,$jstyle) = $styleSrv->getCss();
			$jtitle = $_tmp['image'] ? '<img src="'.$_tmp['image'].'" title="'.$_tmp['title'].'">' : $_tmp['title'];
			if ($jtitle){
				$html .= '<span';
				$html .= $jstyle ? ' style="'.$jstyle.'"' : '';
				$html .= '>';
				$html .= $_tmp['link']? '<a href="'.$_tmp['link'].'">': '';
				$html .= $jtitle;
				$html .= $_tmp['link']? '</a>': '';
				$html .= '</span>';
			}
		}
		if ($titles['background']) {
			$bg = array('background'=>$titles['background']);
			$styleSrv->setStyle($bg);
			list($dom, $background) = $styleSrv->getCss();
		}
		$bgStyle = $background ? '  style="'.$background.'"' : '';
		if ($html) $html = '<h2 class="cc design_tmode_h2"'.$bgStyle.'>'.$html.'</h2>';
		return $html;
	}
	
	private function _setModule() {
		$this->_module = Wekit::load('design.PwDesignModule')->getModule($this->moduleid);
	}
	
}
?>